<?php

namespace App\Services;

use App\Models\TransactionOrder;
use App\Models\TransactionOrderItem;
use App\Models\StoreStock;
use App\Models\Product;
use App\Scopes\StoreScope;
use App\Scopes\EnterpriseScope;
use App\Http\Requests\TransactionOrderCreateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Arr;
use Exception;

class TransactionOrderService
{
    public function __construct(
        TransactionOrder $orderModel,
        Product $productModel,
        StoreStock $stockModel
    )
    {
        $this->orderModel = $orderModel;
        $this->productModel = $productModel;
        $this->stockModel = $stockModel;
    }

    public function paginate(Request $request)
    {
        return $this->orderModel->paginate($request->get('per_page', 10));
    }

    public function get(Request $request, TransactionOrder $data)
    {
        if (!$data) return null;
        return $this->orderModel->where('transaction_orders.id', $data->id)->first();
    }

    public function create(TransactionOrderCreateRequest $request)
    {
        $user = $request->user();
        $payload = $this->getPayloadItems($request);
        DB::beginTransaction();
        try {
            $order = $this->orderModel->create([
                'enterprise_id' => $user->enterprise_id,
                'store_id' => $user->store_id,
                'user_id' => $user->id,
                'total' => $payload->get('total'),
            ]);
            $order->items()->saveMany($payload->get('items'));
            DB::commit();
            return $order;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function getPayloadItems(TransactionOrderCreateRequest $request) {
        $user = $request->user();
        $products = $this->getProducts($request);

        $items = collect([]);
        $error = [];
        $total = 0;

        foreach($request->all() as $index => $v) {

            $product = $products->where('id', $v['product_id'])->first();
            $amount = (int)$v['amount'];

            // Validation Stock
            if (!$product){
                $error["$index.product_id"] = 'does not exists';
                continue;
            }
            if ($product->stock == null) {
                $error["$index.amount"] = "stock does not available in this store";
                continue;
            }
            if ($product->stock < $amount) {
                $error["$index.amount"] = "stock insufficient, available {$product->stock}";
                continue;
            }

            // Calculation
            $subtotal = (int)$product->price * $amount;
            $total += $subtotal;

            // Payload Create Order Item
            $items->push(new TransactionOrderItem([
                'user_id' => $user->id,
                'enterprise_id' => $user->enterprise_id,
                'store_id' => $user->store_id,
                'product_id' => $product->id,
                'amount' => $amount,
                'subtotal' => $subtotal,
            ]));
        }

        // Validate
        if ($error) throw ValidationException::withMessages($error);

        return collect(['items' => $items, 'total' => $total]);
    }

    private function getProducts(TransactionOrderCreateRequest $request)
    {
        $productIds = $request->only('*.product_id')['*']['product_id'];
        return $this->productModel
            ->withoutGlobalScope(EnterpriseScope::class)
            ->where('products.enterprise_id', $request->user()->enterprise_id)
            ->whereIn('products.id', $productIds)
            ->leftJoin('store_stocks', 'store_stocks.product_id', 'products.id')
            ->select(
                'products.*',
                'store_stocks.stock',
                'store_stocks.store_id',
                'store_stocks.id as stock_id',
                'store_stocks.product_id',
            )
            ->get();
    }

    // This the former method,
    // not used due to sqlite does not support right join when testing
    private function getProductUsingStock(TransactionOrderCreateRequest $request)
    {
        $user = $request->user();
        $productIds = $request->only('*.product_id')['*']['product_id'];
        return $this->stockModel
            ->withoutGlobalScope(StoreScope::class)
            ->where('products.enterprise_id', $user->enterprise_id)
            ->where('products.store_id', $user->store_id)
            ->whereIn('products.id', $productIds)
            ->rightJoin('products', 'products.id', 'store_stocks.product_id')
            ->distinct('products.id')
            ->select(
                'products.*',
                'store_stocks.stock',
                'store_stocks.store_id',
                'store_stocks.id as stock_id',
                'store_stocks.product_id',
            )
            ->get();
    }
}

<?php

namespace App\Services;

use App\Models\Store;
use App\Http\Requests\StoreCreateRequest;
use App\Http\Requests\StoreUpdateRequest;
use Illuminate\Http\Request;

class StoreService
{
    public function __construct(Store $storeModel)
    {
        $this->storeModel = $storeModel;
    }

    public function paginate(Request $request)
    {
        return $this->storeModel
            ->ofEnterprise($request->user())
            ->paginate($request->get('per_page', 10));
    }

    public function get(Request $request, Store $data)
    {
        return $data;
    }

    public function create(StoreCreateRequest $request)
    {
        $payload = $request->only($this->storeModel->getFillable());
        return $this->storeModel->create(array_merge(
            $payload,
            ['enterprise_id' => $request->user()->enterprise_id]
        ));
    }

    public function update(StoreUpdateRequest $request, Store $data)
    {
        if (!$data) return null;
        $payload = $request->only($data->getFillable());
        $data->update($payload);
        return $data;
    }

    public function delete(Request $request, Store $data) {
        if(!$data) return null;
        $data->delete();
        return $data;
    }
}

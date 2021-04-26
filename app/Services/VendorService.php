<?php

namespace App\Services;

use App\Models\Vendor;
use App\Http\Requests\VendorCreateRequest;
use App\Http\Requests\VendorUpdateRequest;
use Illuminate\Http\Request;

class VendorService
{
    public function __construct(Vendor $vendorModel)
    {
        $this->vendorModel = $vendorModel;
    }

    public function paginate(Request $request)
    {
        return $this->vendorModel
            ->ofEnterprise($request->user())
            ->paginate($request->get('per_page', 10));
    }

    public function get(Request $request, Vendor $data)
    {
        return $data;
    }

    public function create(VendorCreateRequest $request)
    {
        $payload = $request->only($this->vendorModel->getFillable());
        return $this->vendorModel->create(array_merge(
            $payload,
            ['enterprise_id' => $request->user()->enterprise_id]
        ));
    }

    public function update(VendorUpdateRequest $request, Vendor $data)
    {
        if (!$data) return null;
        $payload = $request->only($data->getFillable());
        $data->update($payload);
        return $data;
    }

    public function delete(Request $request, Vendor $data) {
        if(!$data) return null;
        $data->delete();
        return $data;
    }
}

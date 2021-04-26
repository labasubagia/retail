<?php

namespace App\Services;

use App\Models\Enterprise;
use App\Http\Requests\EnterpriseCreateRequest;
use App\Http\Requests\EnterpriseUpdateRequest;
use Illuminate\Http\Request;

class EnterpriseService
{
    public function __construct(Enterprise $enterpriseModel)
    {
        $this->enterpriseModel = $enterpriseModel;
    }

    public function paginate(Request $request)
    {
        return $this->enterpriseModel->paginate($request->get('per_page', 10));
    }

    public function get(Request $request, Enterprise $data)
    {
        return $data;
    }

    public function create(EnterpriseCreateRequest $request)
    {
        $payload = $request->only($this->enterpriseModel->getFillable());
        return $this->enterpriseModel->create($payload);
    }

    public function update(EnterpriseUpdateRequest $request, Enterprise $data)
    {
        if (!$data) return null;
        $payload = $request->only($data->getFillable());
        $data->update($payload);
        return $data;
    }

    public function delete(Request $request, Enterprise $data) {
        if(!$data) return null;
        $data->delete();
        return $data;
    }
}

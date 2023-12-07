<?php

namespace App\Services\ModelServices;

use App\Http\Resources\EnterpriseResource;
use App\Models\Enterprise;

class EnterpriseService extends BaseService
{
    public function __construct(Enterprise $enterprise)
    {
        $this->model = $enterprise;
    }

    public function get($input)
    {
        $search = $input['search'] ?? '';

        $query = $this->model->with('users')->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($search).'%'])->whereNot('name', 'Admin');
        $data = $this->getAll($input, $query);
        $data['items'] = EnterpriseResource::collection($data['items']);

        return $data;
    }

    public function isExisted($name)
    {
        return Enterprise::where('name', $name)->exists();
    }
}

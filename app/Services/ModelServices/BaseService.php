<?php

namespace App\Services\ModelServices;
use App\Constants\UtilConstant;
use App\Services\BusinessServices\FormResponseService;

class BaseService {
    protected $model;
    
    protected $formResponseService;

    public function create($data) {
        return $this->model->create($data);
    }

    public function update($id, $data) {
        return $this->model->where('id', $id)->update($id, $data);
    }

    public function delete($id) {
        return $this->model->where('id', $id)->delete();
    }

    public function getFirst($id) {
        return $this->model->where('id', $id)->first();
    }

    public function getAll($input, $query = null) {
        if(!$query) $query = $this->model->query();
        $limit =  $input["limit"] ?? UtilConstant::LIMIT_RECORD;
        $column = $input["column"] ?? UtilConstant::COLUMN_DEFAULT;
        $order = $input["order"] ?? UtilConstant::ORDER_TYPE;

        $data = $query->orderBy($column, $order)->paginate($limit);

        return [
            'items' => $data->items(),
            'pagination' => $this->getPaginationData($data)
        ];
    }

    public function getPaginationData($data) {
        $pagination = [
            'perPage' => $data->perPage(),
            'currentPage' => $data->currentPage(),
            'lastPage' => $data->lastPage(),
            'totalRow' => $data->total(),
        ];

        return $pagination;
    }
}
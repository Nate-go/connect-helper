<?php

namespace App\Services\ModelServices;
use App\Services\BusinessServices\FormResponseService;

class BaseService {
    protected $model;
    
    protected $formResponseService;

    public function __construct($model) {
        $this->model = app()->make($model);
        $this->formResponseService = app()->make(FormResponseService::class);
    }

    public function create($data) {
        return $this->model->create($data);
    }

    public function update($id, $data) {
        return $this->model->where('id', $id)->update($id, $data);
    }

    public function delete($id) {
        return $this->model->where('id', $id)->delete();
    }

    public function find($id) {
        return $this->model->find($id);
    }

    public function getFirst($id) {
        return $this->model->where('id', $id)->first();
    }
}
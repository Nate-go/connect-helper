<?php

namespace App\Services\ModelServices;
use App\Constants\AuthenConstant\EncryptionKey;
use App\Constants\UtilConstant;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class BaseService {
    protected $model;
    
    protected $formResponseService;

    public function create($data) {
        if(!$data) return null;
        return $this->model->create($data);
    }

    public function update($ids, $data) {
        try {
            $this->model->whereIn('id', $ids)->update($data);
        }
        catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function delete($ids) {
        return $this->model->destroy($ids);
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

    protected function encryptToken($data)
    {
        $key = Key::loadFromAsciiSafeString(EncryptionKey::REFRESH_KEY);
        $encryptedData = Crypto::encrypt(json_encode($data), $key);
        return $encryptedData;
    }

    protected function decryptToken($encryptedData)
    {
        $key = Key::loadFromAsciiSafeString(EncryptionKey::REFRESH_KEY);
        $decryptedData = Crypto::decrypt($encryptedData, $key);
        return json_decode($decryptedData, true);
    }

    protected function getColumn($data, $column='id') {
        $items = [];
        foreach ($data as $item) {
            $items[] = $item->$column;
        }
        return $items;
    }
}
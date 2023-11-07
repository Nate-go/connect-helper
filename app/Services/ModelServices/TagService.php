<?php

namespace App\Services\ModelServices;
use App\Models\Tag;

class TagService extends BaseService
{
    public function __construct(Tag $tag) {
        $this->model = $tag;
    }

    public function getAllTags() {
        return $this->model->where('user_id', auth()->user()->id)->get();
    }

    public function create($data) {
        $name = $data['name'];

        $tag = $this->model->where('name', $name)->first();

        if ($tag) return null;
        return parent::create(array_merge($data, ['user_id' => auth()->user()->id]));
    }

    public function detail($id) {
        $tag = $this->model->where('id', $id)->first();
        if (!$tag) return null;
        return [
            'tag' => $tag,
            'connections' => $tag->connections->map(function($connection) {
                return [
                    'id' => $connection->id,
                    'name'=> $connection->name,
                ];
            }),
        ];
    }

    public function delete($ids) {
        if(empty($ids)) return false;

        $tags = $this->model->whereIn('id', $ids)->get();

        foreach ($tags as $tag) {
            $tag->deleteConnectionTags();
            $tag->delete();
        }
    }
}
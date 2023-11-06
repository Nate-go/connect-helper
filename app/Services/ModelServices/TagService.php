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
}
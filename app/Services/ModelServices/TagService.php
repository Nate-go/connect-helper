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
}
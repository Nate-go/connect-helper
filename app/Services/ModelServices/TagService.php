<?php

namespace App\Services\ModelServices;

use App\Constants\TagConstant\DefaultTagContent;
use App\Models\Tag;

class TagService extends BaseService
{
    public function __construct(Tag $tag)
    {
        $this->model = $tag;
    }

    public function setUp($user)
    {
        $tags = DefaultTagContent::DEFAULT_TAGS;

        foreach ($tags as $tag) {
            $this->model->create([
                'user_id' => $user->id,
                'name' => $tag,
            ]);
        }
    }

    public function getAllTags()
    {
        return $this->model->with('connections')->where('user_id', auth()->user()->id)->get()->map(function ($tag) {
            return [
                'id' => $tag->id,
                'name' => $tag->name,
                'connections' => $tag->connections->map(function ($connection) {
                    return [
                        'id' => $connection->id,
                        'name' => $connection->name,
                    ];
                }),
            ];
        });
    }

    public function create($data)
    {
        $name = $data['name'];

        $tag = $this->model->where('name', $name)->first();

        if ($tag) {
            return null;
        }

        return parent::create(array_merge($data, ['user_id' => auth()->user()->id]));
    }

    public function detail($id)
    {
        $tag = $this->model->where('id', $id)->first();
        if (! $tag) {
            return null;
        }

        return [
            'tag' => $tag,
            'connections' => $tag->connections->map(function ($connection) {
                return [
                    'id' => $connection->id,
                    'name' => $connection->name,
                ];
            }),
        ];
    }

    public function delete($ids)
    {
        if (empty($ids)) {
            return false;
        }

        $tags = $this->model->whereIn('id', $ids)->get();

        foreach ($tags as $tag) {
            $tag->deleteConnectionTags();
            $tag->delete();
        }

        return true;
    }
}

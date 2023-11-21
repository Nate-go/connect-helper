<?php

namespace App\Services\ModelServices;
use App\Http\Resources\TemplateGroupResource;
use App\Models\TemplateGroup;

class TemplateGroupService extends BaseService
{
    public function __construct(TemplateGroup $templateGroup) {
        $this->model = $templateGroup;
    }

    public function show($id)
    {
        $template = $this->model->where('id', $id)->first();

        if (!$template)
            return false;

        return $template;
    }

    public function update($id, $input)
    {
        $result = $this->model->where('id', $id)->update($input);

        return $result;
    }

    public function getTemplateGroups($input) {
        $statuses = $input["statuses"] ?? [];
        $search = $input['search'] ?? '';

        $query = $this->model->enterpriseTemplate()->where('name', 'LIKE', '%'.$search.'%')->statusFilter($statuses);
        $data = $this->getAll($input, $query);
        $data['items'] = TemplateGroupResource::collection($data['items']);
        return $data;
    }

    public function delete($ids) {
        $result = $this->model->destroy($ids);
        return $result;
    }
}
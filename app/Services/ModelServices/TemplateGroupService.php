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

        return $template->publicTemplates;
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
        $templateIds = $this->getColumn(auth()->user()->templateGroups, 'id');
        if(!$this->includesAll($ids, $templateIds)) return false;

        $templateGroups = $this->model->whereIn('id', $ids)->get();

        foreach($templateGroups as $templateGroup) {
            $templateGroup->template->delete();
        }

        $result = $this->model->destroy($ids);
        return $result;
    }
}
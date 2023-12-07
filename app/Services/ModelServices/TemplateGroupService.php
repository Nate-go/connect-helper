<?php

namespace App\Services\ModelServices;

use App\Constants\SendMailConstant\SendMailType;
use App\Constants\TemplateConstant\DefaultTemplate;
use App\Constants\TemplateConstant\TemplateStatus;
use App\Constants\UserConstant\UserRole;
use App\Http\Resources\TemplateGroupResource;
use App\Models\TemplateGroup;

class TemplateGroupService extends BaseService
{
    protected $templateService;

    public function __construct(TemplateGroup $templateGroup, TemplateService $templateService)
    {
        $this->model = $templateGroup;
        $this->templateService = $templateService;

    }

    public function show($id)
    {
        $template = $this->model->where('id', $id)->first();

        if (! $template) {
            return false;
        }

        return $template->publicTemplates;
    }

    public function getTemplateGroups($input)
    {
        $statuses = $input['statuses'] ?? [];
        $search = $input['search'] ?? '';

        $query = $this->model->with(['user', 'templates'])->enterpriseTemplate()->where('name', 'LIKE', '%'.$search.'%')->statusFilter($statuses);
        $data = $this->getAll($input, $query);
        $data['items'] = TemplateGroupResource::collection($data['items']);

        return $data;
    }

    public function delete($ids)
    {
        $templateIds = $this->getColumn(auth()->user()->templateGroups, 'id');
        if (! $this->includesAll($ids, $templateIds)) {
            return false;
        }

        $result = $this->model->destroy($ids);

        return $result;
    }

    public function setUp($user)
    {
        if ($user->role !== UserRole::OWNER) {
            return false;
        }

        $templateGroups = DefaultTemplate::TEMPLATE_GROUPS;

        foreach ($templateGroups as $templateGroup) {
            $newTemplateGroup = $this->create([
                'user_id' => $user->id,
                'enterprise_id' => $user->enterprise_id,
                'name' => $templateGroup['name'],
                'status' => TemplateStatus::PUBLIC,
            ]);

            foreach ($templateGroup['templates'] as $template) {
                $this->templateService->create([
                    'template_group_id' => $newTemplateGroup->id,
                    'name' => $template['name'],
                    'subject' => $template['subject'],
                    'content' => $template['content'],
                    'type' => SendMailType::PERSONAL,
                    'status' => TemplateStatus::PUBLIC,
                ]);
            }
        }

        return true;
    }
}

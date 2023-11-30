<?php

namespace App\Services\ModelServices;

use App\Http\Resources\TemplatesReviewResource;
use App\Models\Template;
use App\Models\TemplateGroup;

class TemplateService extends BaseService
{
    public function __construct(Template $template)
    {
        $this->model = $template;
    }

    public function show($id)
    {
        $template = $this->model->where('id', $id)->first();

        if (! $template) {
            return false;
        }

        return $template;
    }

    public function getUseableTemplate()
    {
        return TemplatesReviewResource::collection(TemplateGroup::with('publicTemplates')->enterpriseTemplate()->get());
    }
}

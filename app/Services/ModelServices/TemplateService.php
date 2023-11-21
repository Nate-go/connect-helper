<?php

namespace App\Services\ModelServices;
use App\Constants\TemplateConstant\TemplateStatus;
use App\Models\Template;

class TemplateService extends BaseService
{
    public function __construct(Template $template) {
        $this->model = $template;
    }

    public function show($id) {
        $template = $this->model->where('id', $id)->first();

        if(!$template) return false;

        return $template;
    }

    public function update($id, $input)
    {
        $result = $this->model->where('id', $id)->update($input);

        return $result;
    }
}
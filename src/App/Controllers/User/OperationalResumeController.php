<?php

namespace Src\App\Controllers\User;

use Src\App\Controllers\User\TemplateController;

class OperationalResumeController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/operational-resume/index');
    }
}
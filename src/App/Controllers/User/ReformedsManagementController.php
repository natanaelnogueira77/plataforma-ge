<?php

namespace Src\App\Controllers\User;

use Src\App\Controllers\User\TemplateController;

class ReformedsManagementController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/reformeds-management/index');
    }

    public function list(array $data): void 
    {
        
    }
}
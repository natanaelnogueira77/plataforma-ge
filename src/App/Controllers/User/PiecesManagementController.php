<?php

namespace Src\App\Controllers\User;

use Src\App\Controllers\User\TemplateController;

class PiecesManagementController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/pieces-management/index');
    }

    public function list(array $data): void 
    {
        
    }
}
<?php

namespace Src\App\Controllers\User;

use Src\App\Controllers\User\TemplateController;

class DayProductivityController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/day-productivity/index');
    }

    public function list(array $data): void 
    {
        
    }
}
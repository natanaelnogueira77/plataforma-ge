<?php

namespace Src\App\Controllers\Web;

use Src\App\Controllers\Web\TemplateController;

class HomeController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->redirect('auth.index');
    }
}
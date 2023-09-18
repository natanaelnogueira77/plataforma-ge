<?php

namespace Src\App\Controllers\User;

use Src\App\Controllers\User\TemplateController;
use Src\Data\UserPageData;

class UserController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/index', [
            'cards' => UserPageData::getData($this->router, $this->session->getAuth())
        ]);
    }
}
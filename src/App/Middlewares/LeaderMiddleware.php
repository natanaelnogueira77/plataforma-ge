<?php 

namespace Src\App\Middlewares;

use GTG\MVC\Middleware;

class LeaderMiddleware extends Middleware 
{
    public function handle($router): bool
    {
        $user = $this->session->getAuth();
        if(!$user || !$user->isLeader()) {
            $this->session->setFlash('error', _('Você precisa estar autenticado como líder para acessar essa área!'));
            $this->redirect('auth.index');
            return false;
        }

        return true;
    }
}
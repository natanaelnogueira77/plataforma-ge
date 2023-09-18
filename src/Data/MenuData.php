<?php 

namespace Src\Data;

use GTG\MVC\Router;
use Src\Components\MenuItem;
use Src\Models\User;

class MenuData 
{
    public static function getHeaderMenuItems(Router $router, ?User $user = null, ?array $data = null): array 
    {
        return [
            (new MenuItem())
                ->setType(MenuItem::T_ITEM)
                ->setIcon('nav-link-icon fa fa-home')
                ->setURL($user && $user->isAdmin() ? $router->route('admin.index') : $router->route('user.index'))
                ->setText(_('Início'))
        ];
    }

    public static function getLeftMenuItems(Router $router, ?User $user = null, ?array $data = null): array 
    {
        return array_merge([
            (new MenuItem())
                ->setType(MenuItem::T_HEADING)
                ->setText(_('Painéis')),
            (new MenuItem())
                ->setType(MenuItem::T_ITEM)
                ->setIcon('metismenu-icon pe-7s-display2')
                ->setURL($user && $user->isAdmin() ? $router->route('admin.index') : $router->route('user.index'))
                ->setText(_('Painel Principal')),
        ], $user->isAdmin() ? [
            (new MenuItem())
                ->setType(MenuItem::T_HEADING)
                ->setText(_('Usuários')),
            (new MenuItem())
                ->setType(MenuItem::T_ITEM)
                ->setIcon('metismenu-icon pe-7s-users')
                ->setURL($router->route('admin.users.index'))
                ->setText(_('Usuários')),
            (new MenuItem())
                ->setType(MenuItem::T_ITEM)
                ->setIcon('metismenu-icon pe-7s-user')
                ->setURL($router->route('admin.users.create'))
                ->setText(_('Cadastrar Usuário'))
        ] : []);
    }

    public static function getRightMenuItems(Router $router, ?User $user = null, ?array $data = null): array 
    {
        return array_merge(
            $user ? [
                (new MenuItem())
                    ->setURL($user && $user->isAdmin() ? $router->route('admin.index') : $router->route('user.index'))
                    ->setText(_('Painel Principal')),
                (new MenuItem())
                    ->setURL($router->route('user.edit.index'))
                    ->setText(_('Editar meus Dados')),
                (new MenuItem())
                    ->setURL($router->route('auth.index'))
                    ->setText(_('Voltar ao Início')),
                (new MenuItem())
                    ->setURL($router->route('auth.logout'))
                    ->setText(_('Sair'))
            ] : [
            (new MenuItem())
                ->setURL($router->route('auth.index'))
                ->setText(_('Entrar'))
            ]
        );
    }
}
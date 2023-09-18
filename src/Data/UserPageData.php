<?php 

namespace Src\Data;

use GTG\MVC\Router;
use Src\Models\User;

class UserPageData 
{
    public static function getData(Router $router, User $user, array $data = []): array 
    {
        return array_merge($user->isLeader() ? [
            [
                'title' => _('Controle de Peças'),
                'icon' => 'pe-7s-tools',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Veja o controle de peças do sistema.'),
                'items' => [
                    [
                        'url' => $router->route('user.piecesManagement.index'), 
                        'text' => _('Ver Controle de Peças'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ], 
            [
                'title' => _('Controle de Reformados'),
                'icon' => 'pe-7s-config',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Veja o controle de peças do sistema.'),
                'items' => [
                    [
                        'url' => $router->route('user.reformedsManagement.index'), 
                        'text' => _('Ver Controle de Reformados'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ],
            [
                'title' => _('Produtividade do Dia'),
                'icon' => 'pe-7s-check',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Veja a produtividade do dia.'),
                'items' => [
                    [
                        'url' => $router->route('user.dayProductivity.index'), 
                        'text' => _('Ver Produtividade do Dia'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ]
        ] : [], [
            [
                'title' => _('Resumo Operacional'),
                'icon' => 'pe-7s-tools',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Veja o resumo operacional das entradas e saídas.'),
                'items' => [
                    [
                        'url' => $router->route('user.operationalResume.index'), 
                        'text' => _('Ver Resumo Operacional'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ],
            [
                'title' => _('Controle de Estoque'),
                'icon' => 'pe-7s-server',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Gerencie o controle de estoque dos produtos.'),
                'items' => [
                    [
                        'url' => $router->route('user.stocks.index'), 
                        'text' => _('Ver Controle de Estoque'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ],
            [
                'title' => _('Dar Entrada'),
                'icon' => 'pe-7s-upload',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Dar entrada em um produto.'),
                'items' => [
                    [
                        'url' => $router->route('user.productInputs.index'), 
                        'text' => _('Dar Entrada em Produto'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ],
            [
                'title' => _('Dar Saída'),
                'icon' => 'pe-7s-next-2',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Dar saída em um produto.'),
                'items' => [
                    [
                        'url' => $router->route('user.productOutputs.index'), 
                        'text' => _('Dar Saída em Produto'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ],
            [
                'title' => _('Produtos'),
                'icon' => 'pe-7s-box2',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Gerencie os produtos cadastrados no sistema.'),
                'items' => [
                    [
                        'url' => $router->route('user.products.index'), 
                        'text' => _('Lista de Produtos'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ],
            [
                'title' => _('Colaboradores'),
                'icon' => 'pe-7s-users',
                'bg_color' => 'bg-malibu-beach',
                'shadow_color' => 'card-shadow-info',
                'color' => 'text-white',
                'text' => _('Gerencie os colaboradores cadastrados no sistema.'),
                'items' => [
                    [
                        'url' => $router->route('user.collaborators.index'), 
                        'text' => _('Lista de Colaboradores'), 
                        'icon' => 'pe-7s-note2'
                    ]
                ]
            ]
        ]);
    }
}
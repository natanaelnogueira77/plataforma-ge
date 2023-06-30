<?php

namespace Src\App\Controllers\User;

use Src\App\Controllers\User\TemplateController;

class UserController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();

        $user = $this->session->getAuth();
        if($user->isLeader()) {
            $blocks = [
                [
                    'title' => _('Controle de Peças'),
                    'url' => $this->getRoute('user.piecesManagement.index'),
                    'icon' => 'pe-7s-tools',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para acessar o controle de peças.')
                ],
                [
                    'title' => _('Controle de Reformados'),
                    'url' => $this->getRoute('user.reformedsManagement.index'),
                    'icon' => 'pe-7s-config',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para acessar o controle de reformados.')
                ],
                [
                    'title' => _('Produtividade do Dia'),
                    'url' => $this->getRoute('user.dayProductivity.index'),
                    'icon' => 'pe-7s-check',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para acessar a produtividade do dia.')
                ]
            ];
        } elseif($user->isOperator()) {
            $blocks = [
                [
                    'title' => _('Resumo Operacional'),
                    'url' => $this->getRoute('user.operationalResume.index'),
                    'icon' => 'pe-7s-tools',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para acessar o resumo operacional.')
                ],
                [
                    'title' => _('Controle de Estoque'),
                    'url' => $this->getRoute('user.stocks.index'),
                    'icon' => 'pe-7s-server',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para gerenciar o controle de estoque.')
                ],
                [
                    'title' => _('Dar Entrada'),
                    'url' => $this->getRoute('user.productInputs.index'),
                    'icon' => 'pe-7s-upload',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para dar entrada em um produto.')
                ],
                [
                    'title' => _('Dar Saída'),
                    'url' => $this->getRoute('user.productOutputs.index'),
                    'icon' => 'pe-7s-next-2',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para dar saída em um produto.')
                ],
                [
                    'title' => _('Produtos'),
                    'url' => $this->getRoute('user.products.index'),
                    'icon' => 'pe-7s-box2',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para gerenciar os produtos.')
                ],
                [
                    'title' => _('Colaboradores'),
                    'url' => $this->getRoute('user.collaborators.index'),
                    'icon' => 'pe-7s-users',
                    'color' => 'bg-malibu-beach',
                    'text' => _('Clique para gerenciar os colaboradores.')
                ]
            ];
        }

        $this->render('user/index', [
            'blocks' => $blocks
        ]);
    }
}
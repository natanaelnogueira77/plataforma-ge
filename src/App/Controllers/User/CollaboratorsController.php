<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\ExcelGenerator;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Collaborator;
use Src\Utils\ErrorMessages;

class CollaboratorsController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/collaborators/index');
    }

    public function show(array $data): void 
    {
        if(!$dbCollaborator = (new Collaborator())->findById(intval($data['collaborator_id']))) {
            $this->setMessage('error', _('Nenhum colaborador foi encontrado!'))->APIResponse([], 404);
            return;
        }

        $this->APIResponse([
            'content' => $dbCollaborator->getData(),
            'save' => [
                'action' => $this->getRoute('user.collaborators.update', ['collaborator_id' => $dbCollaborator->id]),
                'method' => 'put'
            ]
        ], 200);
    }

    public function store(array $data): void 
    {
        $dbCollaborator = new Collaborator();
        if(!$dbCollaborator->loadData(['usu_id' => $this->session->getAuth()->id] + $data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbCollaborator->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', sprintf(_('O colaborador "%s" foi cadastrado com sucesso!'), $dbCollaborator->name)
        )->APIResponse(['content' => $dbCollaborator->getData()], 200);
    }

    public function update(array $data): void 
    {
        if(!$dbCollaborator = (new Collaborator())->findById(intval($data['collaborator_id']))) {
            $this->setMessage('error', _('Nenhum colaborador foi encontrado!'))->APIResponse([], 404);
            return;
        } elseif(!$dbCollaborator->loadData($data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbCollaborator->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', sprintf(_('Os dados do colaborador "%s" foram alterados com sucesso!'), $dbCollaborator->name)
        )->APIResponse(['content' => $dbCollaborator->getData()], 200);
    }

    public function list(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $content = [];
        $filters = [];

        $limit = $data['limit'] ? intval($data['limit']) : 10;
        $page = $data['page'] ? intval($data['page']) : 1;
        $order = $data['order'] ? $data['order'] : 'id';
        $orderType = $data['orderType'] ? $data['orderType'] : 'ASC';

        if($data['search']) {
            $filters['search'] = [
                'term' => $data['search'],
                'columns' => ['name']
            ];
        }

        $collaborators = (new Collaborator())->get($filters)->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $collaborators->count();
        $pages = ceil($count / $limit);
        
        if($objects = $collaborators->fetch(true)) {
            foreach($objects as $collaborator) {
                $params = ['collaborator_id' => $collaborator->id];
                $content[] = [
                    'id' => '#' . $collaborator->id,
                    'name' => $collaborator->name,
                    'created_at' => $collaborator->getCreatedAtDateTime()->format('d/m/Y'),
                    'actions' => "
                        <div class=\"dropup d-inline-block\">
                            <button type=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\" 
                                data-toggle=\"dropdown\" class=\"dropdown-toggle btn btn-sm btn-primary\">
                                " . _('Ações') . "
                            </button>
                            <div tabindex=\"-1\" role=\"menu\" aria-hidden=\"true\" class=\"dropdown-menu\">
                                <h6 tabindex=\"-1\" class=\"dropdown-header\">" . _('Ações') . "</h6>
                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"edit\" data-method=\"get\" 
                                    data-action=\"{$this->getRoute('user.collaborators.show', $params)}\">
                                    " . _('Editar Colaborador') . "
                                </button>

                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"delete\" data-method=\"delete\" 
                                    data-action=\"{$this->getRoute('user.collaborators.delete', $params)}\">
                                    " . _('Excluir Colaborador') . "
                                </button>
                            </div>
                        </div>
                    "
                ];
            }
        }

        $this->APIResponse([
            'content' => [
                'table' => $this->getView('components/data-table', [
                    'headers' => [
                        'actions' => ['text' => _('Ações')],
                        'id' => ['text' => _('ID'), 'sort' => true],
                        'name' => ['text' => _('Nome'), 'sort' => true],
                        'created_at' => ['text' => _('Criado em'), 'sort' => true]
                    ],
                    'order' => [
                        'selected' => $order,
                        'type' => $orderType
                    ],
                    'data' => $content
                ]),
                'pagination' => $this->getView('components/pagination', [
                    'pages' => $pages,
                    'currPage' => $page,
                    'results' => $count,
                    'limit' => $limit
                ])
            ]
        ], 200);
    }

    public function delete(array $data): void 
    {
        if(!$dbCollaborator = (new Collaborator())->findById(intval($data['collaborator_id']))) {
            $this->setMessage('error', _('Nenhum colaborador foi encontrado!'))->APIResponse([], 404);
            return;
        } elseif(!$dbCollaborator->destroy()) {
            $this->setMessage('error', _('Não foi possível excluir o colaborador!'))->APIResponse([], 422);
            return;
        }

        $this->setMessage('success', sprintf(_('O colaborador "%s" foi excluído com sucesso.'), $dbCollaborator->name))
            ->APIResponse([], 200);
    }

    public function export(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $excelData = [];
        $filters = [];

        if($collaborators = (new Collaborator())->get($filters)->fetch(true)) {
            foreach($collaborators as $collaborator) {
                $excelData[] = [
                    _('Nome') => $collaborator->name
                ];
            }
        }

        $excel = new ExcelGenerator($excelData, _('Lista de Colaboradores'));
        if(!$excel->render()) {
            $this->session->setFlash('error', ErrorMessages::excel());
            $this->redirect('user.collaborators.index');
        }

        $excel->stream();
    }
}
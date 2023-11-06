<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\ExcelGenerator;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Collaborator;
use Src\Models\Product;
use Src\Models\ProductOutput;
use Src\Models\User;
use Src\Utils\ErrorMessages;

class ProductOutputsController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/product-outputs/index', [
            'dbProducts' => (new Product())->get()->order('desc_short')->fetch(true),
            'dbCollaborators' => (new Collaborator())->get()->order('name')->fetch(true)
        ]);
    }

    public function show(array $data): void 
    {
        if(!$dbProductOutput = (new ProductOutput())->findById(intval($data['product_output_id']))) {
            $this->setMessage('error', _('Nenhuma saída foi encontrada!'))->APIResponse([], 404);
            return;
        }

        $this->APIResponse([
            'content' => $dbProductOutput->getData(),
            'save' => [
                'action' => $this->getRoute('user.productOutputs.update', ['product_output_id' => $dbProductOutput->id]),
                'method' => 'put'
            ]
        ], 200);
    }

    public function store(array $data): void 
    {
        $dbProductOutput = new ProductOutput();
        if(!$dbProductOutput->loadData(['usu_id' => $this->session->getAuth()->id] + $data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbProductOutput->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', sprintf(
                _('A saída do produto "%s" foi realizada com sucesso!'), 
                $dbProductOutput->product()->desc_short
            )
        )->APIResponse([], 200);
    }

    public function update(array $data): void 
    {
        if(!$dbProductOutput = (new ProductOutput())->findById(intval($data['product_output_id']))) {
            $this->setMessage('error', _('Nenhuma saída foi encontrada!'))->APIResponse([], 404);
            return;
        }
        
        $dbProductOutput->loadData([
            'boxes' => $data['boxes'], 
            'units' => $data['units']
        ]);
        
        if(!$dbProductOutput->loadData($data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbProductOutput->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(
                _('Os dados da saída do produto "%s" foram alterados com sucesso!'), 
                $dbProductOutput->product()->desc_short
            )
        )->APIResponse([], 200);
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
                'columns' => ['id']
            ];
        }

        if($data['product_id']) {
            $filters['pro_id'] = $data['product_id'];
        }

        if($data['collaborator_id']) {
            $filters['col_id'] = $data['collaborator_id'];
        }

        $productOutputs = (new ProductOutput())->get($filters)->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $productOutputs->count();
        $pages = ceil($count / $limit);
        
        if($objects = $productOutputs->fetch(true)) {
            $objects = ProductOutput::withProduct($objects);
            $objects = ProductOutput::withCollaborator($objects);
            foreach($objects as $productOutput) {
                $params = ['product_output_id' => $productOutput->id];
                $content[] = [
                    'id' => '#' . $productOutput->id,
                    'pro_id' => $productOutput->product->desc_short,
                    'boxes' => $productOutput->boxes,
                    'units' => $productOutput->units,
                    'col_id' => $productOutput->collaborator->name,
                    'created_at' => $productOutput->getCreatedAtDateTime()->format('d/m/Y'),
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
                                    data-action=\"{$this->getRoute('user.productOutputs.show', $params)}\">
                                    " . _('Editar Saída') . "
                                </button>

                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"delete\" data-method=\"delete\" 
                                    data-action=\"{$this->getRoute('user.productOutputs.delete', $params)}\">
                                    " . _('Excluir Saída') . "
                                </button>
                            </div>
                        </div>
                    "
                ];
            }
        }

        $this->APIResponse([
            'content' => [
                'table' => $this->getView('_components/data-table', [
                    'headers' => [
                        'actions' => ['text' => _('Ações')],
                        'id' => ['text' => _('ID'), 'sort' => true],
                        'pro_id' => ['text' => _('Descrição'), 'sort' => true],
                        'boxes' => ['text' => _('Caixas'), 'sort' => true],
                        'units' => ['text' => _('Unidades'), 'sort' => true],
                        'col_id' => ['text' => _('Colaborador'), 'sort' => true],
                        'created_at' => ['text' => _('Criado em'), 'sort' => true]
                    ],
                    'order' => [
                        'selected' => $order,
                        'type' => $orderType
                    ],
                    'data' => $content
                ]),
                'pagination' => $this->getView('_components/pagination', [
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
        if(!$dbProductOutput = (new ProductOutput())->findById(intval($data['product_output_id']))) {
            $this->setMessage('error', _('Nenhuma saída foi encontrada!'))->APIResponse([], 404);
            return;
        } elseif(!$dbProductOutput->destroy()) {
            $this->setMessage('error', _('Não foi possível excluir a saída!'))->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(
                _('A saída do produto "%s" foi excluído com sucesso.'), 
                $dbProductOutput->product()->desc_short
            )
        )->APIResponse([], 200);
    }

    public function export(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $excelData = [];
        $filters = [];

        if($data['product_id']) {
            $filters['pro_id'] = $data['product_id'];
        }

        if($data['collaborator_id']) {
            $filters['col_id'] = $data['collaborator_id'];
        }

        if($productOutputs = (new ProductOutput())->get($filters)->fetch(true)) {
            $productOutputs = ProductOutput::withCollaborator($productOutputs);
            $productOutputs = ProductOutput::withProduct($productOutputs);
            foreach($productOutputs as $productOutput) {
                $excelData[] = [
                    _('Descrição') => $productOutput->product->desc_short,
                    _('Quantidade de caixas') => $productOutput->boxes,
                    _('Quantidade de unidades') => $productOutput->units,
                    _('Colaborador') =>  $productOutput->collaborator->name,
                    _('Data de saída') => $productOutput->getCreatedAtDateTime()->format('d/m/Y'),
                    _('Hora de saída') => $productOutput->getCreatedAtDateTime()->format('H:i')
                ];
            }
        }

        $excel = new ExcelGenerator($excelData, _('Lista de Saidas'));
        if(!$excel->render()) {
            $this->session->setFlash('error', ErrorMessages::excel());
            $this->redirect('user.productOutputs.index');
        }

        $excel->stream();
    }
}
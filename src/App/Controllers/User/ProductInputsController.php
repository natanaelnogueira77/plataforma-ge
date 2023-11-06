<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\ExcelGenerator;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Product;
use Src\Models\ProductInput;
use Src\Models\User;
use Src\Utils\ErrorMessages;

class ProductInputsController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/product-inputs/index', [
            'dbProducts' => (new Product())->get()->order('desc_short')->fetch(true),
            'dbUsers' => (new User())->get()->order('name')->fetch(true),
            'states' => ProductInput::getStates()
        ]);
    }

    public function show(array $data): void 
    {
        if(!$dbProductInput = (new ProductInput())->findById(intval($data['product_input_id']))) {
            $this->setMessage('error', _('Nenhuma entrada foi encontrada!'))->APIResponse([], 404);
            return;
        }

        $this->APIResponse([
            'content' => $dbProductInput->getData(),
            'save' => [
                'action' => $this->getRoute('user.productInputs.update', ['product_input_id' => $dbProductInput->id]),
                'method' => 'put'
            ]
        ], 200);
    }

    public function store(array $data): void 
    {
        $dbProductInput = new ProductInput();
        if(!$dbProductInput->loadData(['usu_id' => $this->session->getAuth()->id] + $data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbProductInput->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(
                _('A entrada do produto "%s" foi realizada com sucesso!'), 
                $dbProductInput->product()->desc_short
            )
        )->APIResponse([], 200);
    }

    public function update(array $data): void 
    {
        if(!$dbProductInput = (new ProductInput())->findById(intval($data['product_input_id']))) {
            $this->setMessage('error', _('Nenhuma entrada foi encontrada!'))->APIResponse([], 404);
            return;
        }
        
        $dbProductInput->loadData([
            'boxes' => $data['boxes'], 
            'units' => $data['units'], 
            'street' => $data['street'], 
            'position' => $data['position'], 
            'height' => $data['height'], 
            'c_status' => $data['c_status']
        ]);

        if(!$dbProductInput->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbProductInput->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(
                _('Os dados da entrada do produto "%s" foram alterados com sucesso!'), 
                $dbProductInput->product()->desc_short
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

        if($data['status']) {
            $filters['c_status'] = $data['status'];
        } else {
            $filters['!='] = ['c_status' => ProductInput::CS_RECEIVED];
        }

        $productInputs = (new ProductInput())->get($filters)->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $productInputs->count();
        $pages = ceil($count / $limit);
        
        if($objects = $productInputs->fetch(true)) {
            $objects = ProductInput::withProduct($objects);
            foreach($objects as $productInput) {
                $params = ['product_input_id' => $productInput->id];
                $content[] = [
                    'id' => '#' . $productInput->id,
                    'pro_id' => $productInput->product->desc_short,
                    'boxes' => $productInput->boxes,
                    'units' => $productInput->units,
                    'street' => $productInput->street ?? '---',
                    'position' => $productInput->position ?? '---',
                    'height' => $productInput->height ?? '---',
                    'c_status' => "<div class=\"badge badge-{$productInput->getStatusColor()}\">{$productInput->getStatus()}</div>",
                    'created_at' => $productInput->getCreatedAtDateTime()->format('d/m/Y'),
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
                                    data-action=\"{$this->getRoute('user.productInputs.show', $params)}\">
                                    " . _('Editar Entrada') . "
                                </button>

                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"delete\" data-method=\"delete\" 
                                    data-action=\"{$this->getRoute('user.productInputs.delete', $params)}\">
                                    " . _('Excluir Entrada') . "
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
                        'street' => ['text' => _('Rua'), 'sort' => true],
                        'position' => ['text' => _('Posição'), 'sort' => true],
                        'height' => ['text' => _('Altura'), 'sort' => true],
                        'c_status' => ['text' => _('Observações'), 'sort' => true],
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
        if(!$dbProductInput = (new ProductInput())->findById(intval($data['product_input_id']))) {
            $this->setMessage('error', _('Nenhuma entrada foi encontrada!'))->APIResponse([], 404);
            return;
        } elseif(!$dbProductInput->destroy()) {
            if($dbProductInput->hasError('destroy')) {
                $this->setMessage('error', $dbProductInput->getFirstError('destroy'))->APIResponse([], 422);
            } else {
                $this->setMessage('error', _('Não foi possível excluir a entrada!'))->APIResponse([], 500);
            }
            return;
        }

        $this->setMessage(
            'success', sprintf(_('A entrada do produto "%s" foi excluído com sucesso.'), $dbProductInput->product()->desc_short)
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

        if($data['status']) {
            $filters['c_status'] = $data['status'];
        } else {
            $filters['!='] = ['c_status' => ProductInput::CS_RECEIVED];
        }

        if($productInputs = (new ProductInput())->get($filters)->fetch(true)) {
            $productInputs = ProductInput::withProduct($productInputs);
            foreach($productInputs as $productInput) {
                $excelData[] = [
                    _('Descrição') => $productInput->product->desc_short,
                    _('Quantidade de caixas') => $productInput->boxes,
                    _('Quantidade de unidades') => $productInput->units,
                    _('Rua') => $productInput->street ?? '---',
                    _('Posição') => $productInput->position ?? '---',
                    _('Altura') => $productInput->height ?? '---',
                    _('Status') => $productInput->getStatus(),
                    _('Data de entrada') => $productInput->getCreatedAtDateTime()->format('d/m/Y'),
                    _('Hora de entrada') => $productInput->getCreatedAtDateTime()->format('H:i')
                ];
            }
        }

        $excel = new ExcelGenerator($excelData, _('Lista de Entradas'));
        if(!$excel->render()) {
            $this->session->setFlash('error', ErrorMessages::excel());
            $this->redirect('user.productInputs.index');
        }

        $excel->stream();
    }
}
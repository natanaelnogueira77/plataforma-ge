<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\ExcelGenerator;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Product;
use Src\Utils\ErrorMessages;

class ProductsController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/products/index');
    }

    public function show(array $data): void 
    {
        if(!$dbProduct = (new Product())->findById(intval($data['product_id']))) {
            $this->setMessage('error', _('Nenhum produto foi encontrado!'))->APIResponse([], 404);
            return;
        }

        $this->APIResponse([
            'content' => $dbProduct->getData(),
            'save' => [
                'action' => $this->getRoute('user.products.update', ['product_id' => $dbProduct->id]),
                'method' => 'put'
            ]
        ], 200);
    }

    public function store(array $data): void 
    {
        $dbProduct = new Product();
        if(!$dbProduct->loadData(['usu_id' => $this->session->getAuth()->id] + $data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbProduct->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('O produto "%s" foi cadastrado com sucesso!'), $dbProduct->desc_short)
        )->APIResponse([], 200);
    }

    public function update(array $data): void 
    {
        if(!$dbProduct = (new Product())->findById(intval($data['product_id']))) {
            $this->setMessage('error', _('Nenhum produto foi encontrado!'))->APIResponse([], 404);
            return;
        } elseif(!$dbProduct->loadData($data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbProduct->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('Os dados do produto "%s" foram alterados com sucesso!'), $dbProduct->desc_short)
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
                'columns' => ['desc_short']
            ];
        }

        $products = (new Product())->get($filters)->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $products->count();
        $pages = ceil($count / $limit);
        
        if($objects = $products->fetch(true)) {
            foreach($objects as $product) {
                $params = ['product_id' => $product->id];
                $content[] = [
                    'id' => '#' . $product->id,
                    'desc_short' => $product->desc_short,
                    'created_at' => $product->getCreatedAtDateTime()->format('d/m/Y'),
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
                                    data-action=\"{$this->getRoute('user.products.show', $params)}\">
                                    " . _('Editar Produto') . "
                                </button>

                                <button type=\"button\" tabindex=\"0\" class=\"dropdown-item\" 
                                    data-act=\"delete\" data-method=\"delete\" 
                                    data-action=\"{$this->getRoute('user.products.delete', $params)}\">
                                    " . _('Excluir Produto') . "
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
                        'desc_short' => ['text' => _('Descrição'), 'sort' => true],
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
        if(!$dbProduct = (new Product())->findById(intval($data['product_id']))) {
            $this->setMessage('error', _('Nenhum produto foi encontrado!'))->APIResponse([], 404);
            return;
        } elseif(!$dbProduct->destroy()) {
            $this->setMessage('error', _('Não foi possível excluir o produto!'))->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(_('O produto "%s" foi excluído com sucesso.'), $dbProduct->desc_short)
        )->APIResponse([], 200);
    }

    public function export(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $excelData = [];
        $filters = [];

        if($products = (new Product())->get($filters)->fetch(true)) {
            foreach($products as $product) {
                $excelData[] = [
                    _('Nome') => $product->desc_short
                ];
            }
        }

        $excel = new ExcelGenerator($excelData, _('Lista de Produtos'));
        if(!$excel->render()) {
            $this->session->setFlash('error', ErrorMessages::excel());
            $this->redirect('user.products.index');
        }

        $excel->stream();
    }
}
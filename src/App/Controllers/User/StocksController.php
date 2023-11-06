<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\ExcelGenerator;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Product;
use Src\Models\ProductInput;
use Src\Models\Stock;
use Src\Models\User;
use Src\Utils\ErrorMessages;

class StocksController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/stocks/index', [
            'dbProducts' => (new Product())->get()->order('desc_short')->fetch(true)
        ]);
    }

    public function list(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $tnProductInput = ProductInput::tableName();
        $tnStock = Stock::tableName();

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
            $filters["{$tnStock}.pro_id"] = $data['product_id'];
        }

        $filters["t1.c_status"] = 1;
        $filters['raw'] = "t1.updated_at = (SELECT MAX(t2.updated_at) FROM {$tnProductInput} t2 where t2.pro_id = t1.pro_id)";

        $stocks = (new Stock())->join("{$tnProductInput} t1", [
            'raw' => "t1.pro_id = {$tnStock}.pro_id"
        ])->get($filters, "
            {$tnStock}.*,
            t1.street AS pi_street,
            t1.position AS pi_position,
            t1.height AS pi_height,
            t1.updated_at AS pi_updated_at
        ")->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $stocks->count();
        $pages = ceil($count / $limit);
        
        if($objects = $stocks->fetch(true)) {
            $objects = Stock::withProduct($objects);
            foreach($objects as $stock) {
                $params = ['stock_id' => $stock->id];
                $content[] = [
                    'id' => '#' . $stock->id,
                    'pro_id' => $stock->product->desc_short,
                    'boxes' => $stock->boxes,
                    'units' => $stock->units,
                    'pi_street' => $stock->pi_street ?? '---',
                    'pi_position' => $stock->pi_position ?? '---',
                    'pi_height' => $stock->pi_height ?? '---',
                    'boxes_status' => $stock->boxes 
                        ? "<div class=\"badge badge-success\">" . _('OK') . "</div>" 
                        : "<div class=\"badge badge-danger\">" . _('Vazio') . "</div>",
                    'units_status' => $stock->units 
                        ? "<div class=\"badge badge-success\">" . _('OK') . "</div>" 
                        : "<div class=\"badge badge-danger\">" . _('Vazio') . "</div>",
                    'pi_updated_at' => $this->getDateTime($stock->pi_updated_at)->format('d/m/Y H:i')
                ];
            }
        }

        $this->APIResponse([
            'content' => [
                'table' => $this->getView('_components/data-table', [
                    'headers' => [
                        'id' => ['text' => _('ID'), 'sort' => true],
                        'pro_id' => ['text' => _('Descrição'), 'sort' => true],
                        'boxes' => ['text' => _('Caixas'), 'sort' => true],
                        'units' => ['text' => _('Unidades'), 'sort' => true],
                        'pi_street' => ['text' => _('Rua'), 'sort' => true],
                        'pi_position' => ['text' => _('Posição'), 'sort' => true],
                        'pi_height' => ['text' => _('Altura'), 'sort' => true],
                        'boxes_status' => ['text' => _('Status Caixas'), 'sort' => false],
                        'units_status' => ['text' => _('Status Unidades'), 'sort' => false],
                        'pi_updated_at' => ['text' => _('Última Entrada'), 'sort' => true]
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

    public function export(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $excelData = [];
        $filters = [];

        $tnProductInput = ProductInput::tableName();
        $tnStock = Stock::tableName();

        if($data['product_id']) {
            $filters["{$tnStock}.pro_id"] = $data['product_id'];
        }

        $filters["t1.c_status"] = ProductInput::CS_RECEIVED;
        $filters['raw'] = "t1.updated_at = (SELECT MAX(t2.updated_at) FROM {$tnProductInput} t2 where t2.pro_id = t1.pro_id)";

        $stocks = (new Stock())->join("{$tnProductInput} t1", [
            'raw' => "t1.pro_id = {$tnStock}.pro_id"
        ])->get($filters, "
            {$tnStock}.*,
            t1.street AS pi_street,
            t1.position AS pi_position,
            t1.height AS pi_height,
            t1.updated_at AS pi_updated_at
        ")->fetch(true);

        if($stocks) {
            $stocks = Stock::withProduct($stocks);
            foreach($stocks as $stock) {
                $excelData[] = [
                    _('Descrição') => $stock->product->desc_short,
                    _('Quantidade de caixas') => $stock->boxes,
                    _('Quantidade de unidades') => $stock->units,
                    _('Status caixas') => $stock->boxes ? _('OK') : _('Vazio'),
                    _('Status unidades') => $stock->units ? _('OK') : _('Vazio'),
                    _('Rua') => $stock->pi_street ?? '---',
                    _('Posição') => $stock->pi_position ?? '---',
                    _('Altura') => $stock->pi_height ?? '---',
                    _('Data da última entrada') => $this->getDateTime($stock->pi_updated_at)->format('d/m/Y'),
                    _('Hora da última entrada') => $this->getDateTime($stock->pi_updated_at)->format('H:i')
                ];
            }
        }

        $excel = new ExcelGenerator($excelData, _('Lista de Estoque'));
        if(!$excel->render()) {
            $this->session->setFlash('error', ErrorMessages::excel());
            $this->redirect('user.stocks.index');
        }

        $excel->stream();
    }
}
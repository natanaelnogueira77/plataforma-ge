<?php

namespace Src\App\Controllers\User;

use GTG\MVC\Components\ExcelGenerator;
use Src\App\Controllers\User\TemplateController;
use Src\Models\Product;
use Src\Models\Reformation;
use Src\Models\ReformationTurnEndForm;
use Src\Models\ReformationTurnStartForm;
use Src\Utils\ErrorMessages;

class ReformedsManagementController extends TemplateController 
{
    public function index(array $data): void 
    {
        $this->addData();
        $this->render('user/reformeds-management/index', [
            'dbProducts' => (new Product())->get()->fetch(true)
        ]);
    }

    public function show(array $data): void 
    {
        if(!$dbReformation = (new Reformation())->findById(intval($data['reformation_id']))) {
            $this->setMessage('error', _('Nenhuma entrada foi encontrada!'))->APIResponse([], 404);
            return;
        }

        $this->APIResponse([
            'content' => $dbReformation->getData(),
            'save' => [
                'action' => $this->getRoute('user.reformedsManagement.update', ['reformation_id' => $dbReformation->id]),
                'method' => 'put'
            ]
        ], 200);
    }

    public function store(array $data): void 
    {
        $dbReformation = new Reformation();
        if(!$dbReformation->loadData(['usu_id' => $this->session->getAuth()->id] + $data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbReformation->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage('success', _('A entrada foi cadastrada com sucesso!'))->APIResponse([], 200);
    }

    public function update(array $data): void 
    {
        if(!$dbReformation = (new Reformation())->findById(intval($data['reformation_id']))) {
            $this->setMessage('error', _('Nenhuma entrada foi encontrada!'))->APIResponse([], 404);
            return;
        } elseif(!$dbReformation->loadData($data)->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbReformation->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage('success', _('Os dados da entrada foram alterados com sucesso!'))->APIResponse([], 200);
    }

    public function list(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $content = [];
        $filters = [];

        $tnReformation = Reformation::tableName();

        $limit = $data['limit'] ? intval($data['limit']) : 10;
        $page = $data['page'] ? intval($data['page']) : 1;
        $order = $data['order'] ? $data['order'] : 'id';
        $orderType = $data['orderType'] ? $data['orderType'] : 'ASC';

        $filters["{$tnReformation}.turn"] = 1;

        if($data['product_id']) {
            $filters["{$tnReformation}.pro_id"] = $data['product_id'];
        }

        if($data['r_date']) {
            $filters["{$tnReformation}.r_date"] = $data['r_date'];
        }

        $reformations = (new Reformation())->leftJoin("{$tnReformation} t2", [
            'raw' => "t2.r_date = {$tnReformation}.r_date AND t2.turn = 2 AND t2.pro_id = {$tnReformation}.pro_id"
        ])->leftJoin("{$tnReformation} t3", [
            'raw' => "t3.r_date = {$tnReformation}.r_date AND t3.turn = 3 AND t3.pro_id = {$tnReformation}.pro_id"
        ])->get($filters, "
            {$tnReformation}.id, 
            {$tnReformation}.usu_id, 
            {$tnReformation}.pro_id, 
            {$tnReformation}.r_date, 
            {$tnReformation}.amount_start as t1_amount_start,
            {$tnReformation}.amount_end as t1_amount_end,
            t2.amount_start as t2_amount_start,
            t2.amount_end as t2_amount_end,
            t3.amount_start as t3_amount_start,
            t3.amount_end as t3_amount_end
        ")->paginate($limit, $page)->sort([$order => $orderType]);
        $count = $reformations->count();
        $pages = ceil($count / $limit);
        
        if($objects = $reformations->fetch(true)) {
            $objects = Reformation::withProduct($objects);
            foreach($objects as $reformation) {
                $params = ['reformation_id' => $reformation->id];
                $amountStartTotal = $reformation->t1_amount_start + $reformation->t2_amount_start + $reformation->t3_amount_start;
                $amountEndTotal = $reformation->t1_amount_end + $reformation->t2_amount_end + $reformation->t3_amount_end;
                $balance =  $amountStartTotal - $amountEndTotal;
                $content[] = [
                    'r_date' => $reformation->getReformationDateTime()->format('d/m/Y'),
                    'pro_id' => $reformation->product->desc_short,
                    't1_amount_start' => $reformation->t1_amount_start ?? 0,
                    't2_amount_start' => $reformation->t2_amount_start ?? 0,
                    't3_amount_start' => $reformation->t3_amount_start ?? 0,
                    't1_amount_end' => $reformation->t1_amount_end ?? 0,
                    't2_amount_end' => $reformation->t2_amount_end ?? 0,
                    't3_amount_end' => $reformation->t3_amount_end ?? 0,
                    'total_amount_start' => $amountStartTotal ?? 0,
                    'total_amount_end' => $amountEndTotal ?? 0,
                    'balance' => $balance ?? 0
                ];
            }
        }

        $this->APIResponse([
            'content' => [
                'table' => $this->getView('_components/data-table', [
                    'headers' => [
                        'r_date' => ['text' => _('Data'), 'sort' => true],
                        'pro_id' => ['text' => _('Produto'), 'sort' => true],
                        't1_amount_start' => ['text' => _('Qtd. de Quebrados 1º Turno'), 'sort' => true, 'classes' => 'bg-danger text-white'],
                        't2_amount_start' => ['text' => _('Qtd. de Quebrados 2º Turno'), 'sort' => true, 'classes' => 'bg-danger text-white'],
                        't3_amount_start' => ['text' => _('Qtd. de Quebrados 3º Turno'), 'sort' => true, 'classes' => 'bg-danger text-white'],
                        't1_amount_end' => ['text' => _('Qtd. de Consertados 1º Turno'), 'sort' => true, 'classes' => 'bg-success text-white'],
                        't2_amount_end' => ['text' => _('Qtd. de Consertados 2º Turno'), 'sort' => true, 'classes' => 'bg-success text-white'],
                        't3_amount_end' => ['text' => _('Qtd. de Consertados 3º Turno'), 'sort' => true, 'classes' => 'bg-success text-white'],
                        'total_amount_start' => ['text' => _('Total de Quebrados'), 'sort' => false, 'classes' => 'bg-danger text-white'],
                        'total_amount_end' => ['text' => _('Total de Consertados'), 'sort' => false, 'classes' => 'bg-success text-white'],
                        'balance' => ['text' => _('Saldo'), 'sort' => false]
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
        if(!$dbReformation = (new Reformation())->findById(intval($data['reformation_id']))) {
            $this->setMessage('error', _('Nenhuma entrada foi encontrada!'))->APIResponse([], 404);
            return;
        } elseif(!$dbReformation->destroy()) {
            $this->setMessage('error', _('Não foi possível excluir a entrada!'))->APIResponse([], 422);
            return;
        }

        $this->setMessage('success', _('A entrada foi excluída com sucesso.'))->APIResponse([], 200);
    }

    public function export(array $data): void 
    {
        $data = array_merge($data, filter_input_array(INPUT_GET, FILTER_DEFAULT));

        $excelData = [];
        $filters = [];

        $tnReformation = Reformation::tableName();

        $filters["{$tnReformation}.turn"] = 1;

        if($data['product_id']) {
            $filters["{$tnReformation}.pro_id"] = $data['product_id'];
        }

        if($data['r_date']) {
            $filters["{$tnReformation}.r_date"] = $data['r_date'];
        }

        $reformations = (new Reformation())->leftJoin("{$tnReformation} t2", [
            'raw' => "t2.r_date = {$tnReformation}.r_date AND t2.turn = 2 AND t2.pro_id = {$tnReformation}.pro_id"
        ])->leftJoin("{$tnReformation} t3", [
            'raw' => "t3.r_date = {$tnReformation}.r_date AND t3.turn = 3 AND t3.pro_id = {$tnReformation}.pro_id"
        ])->get($filters, "
            {$tnReformation}.id, 
            {$tnReformation}.usu_id, 
            {$tnReformation}.pro_id, 
            {$tnReformation}.r_date, 
            {$tnReformation}.amount_start as t1_amount_start,
            {$tnReformation}.amount_end as t1_amount_end,
            {$tnReformation}.created_at as created_at,
            {$tnReformation}.updated_at as updated_at,
            t2.amount_start as t2_amount_start,
            t2.amount_end as t2_amount_end,
            t3.amount_start as t3_amount_start,
            t3.amount_end as t3_amount_end
        ")->fetch(true);

        if($reformations) {
            $reformations = Reformation::withProduct($reformations);
            foreach($reformations as $reformation) {
                $amountStartTotal = $reformation->t1_amount_start + $reformation->t2_amount_start + $reformation->t3_amount_start;
                $amountEndTotal = $reformation->t1_amount_end + $reformation->t2_amount_end + $reformation->t3_amount_end;
                $balance =  $amountStartTotal - $amountEndTotal;

                $excelData[] = [
                    _('Data') => $reformation->getReformationDateTime()->format('d/m/Y'),
                    _('Produto') => $reformation->product->desc_short ?? '---',
                    _('Quantidade de quebrados 1º turno') => $reformation->t1_amount_start ?? 0,
                    _('Quantidade de quebrados 2º turno') => $reformation->t2_amount_start ?? 0,
                    _('Quantidade de quebrados 3º turno') => $reformation->t3_amount_start ?? 0,
                    _('Quantidade de consertados 1º turno') => $reformation->t1_amount_end ?? 0,
                    _('Quantidade de consertados 2º turno') => $reformation->t2_amount_end ?? 0,
                    _('Quantidade de consertados 3º turno') => $reformation->t3_amount_end ?? 0,
                    _('Total de quebrados') => $amountStartTotal ?? 0,
                    _('Total de consertados') => $amountEndTotal ?? 0,
                    _('Saldo') => $balance ?? 0,
                    _('Data de entrada') => $reformation->getCreatedAtDateTime()->format('d/m/Y'),
                    _('Hora de entrada') => $reformation->getCreatedAtDateTime()->format('H:i')
                ];
            }
        }

        $excel = new ExcelGenerator($excelData, _('Lista de Reformados'));
        if(!$excel->render()) {
            $this->session->setFlash('error', ErrorMessages::excel());
            $this->redirect('user.reformedsManagement.index');
        }

        $excel->stream();
    }

    public function turnStart(array $data): void 
    {
        $dbRTSF = (new ReformationTurnStartForm())->loadData([
            'usu_id' => $this->session->getAuth()->id,
            'pro_id' => $data['pro_id'] ? intval($data['pro_id']) : null,
            'turn' => $data['turn'] ? intval($data['turn']) : null,
            'amount_start' => $data['amount_start'] ? intval($data['amount_start']) : null,
            'r_date' => $data['r_date']
        ]);
        if(!$dbRTSF->validate()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbRTSF->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $dbReformation = (new Reformation())->get([
            'pro_id' => $dbRTSF->pro_id, 
            'r_date' => $dbRTSF->r_date, 
            'turn' => $dbRTSF->turn
        ])->fetch(false);
        if(!$dbReformation) {
            $dbReformation = new Reformation();
            $dbReformation->usu_id = $dbRTSF->usu_id;
        }
        
        $dbReformation->loadData([
            'pro_id' => $dbRTSF->pro_id,
            'turn' => $dbRTSF->turn, 
            'r_date' => $dbRTSF->r_date,
            'amount_start' => $dbRTSF->amount_start
        ]);
        if(!$dbReformation->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbReformation->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(
                _('A quantidade de quebrados do início do %sº turno do dia %s para o produto "%s" foi registrado com sucesso!'), 
                $dbReformation->turn, 
                $dbReformation->getReformationDateTime()->format('d/m/Y'), 
                $dbReformation->product()->desc_short
            )
        )->APIResponse([], 200);
    }

    public function turnEnd(array $data): void 
    {
        $dbRTEF = (new ReformationTurnEndForm())->loadData([
            'usu_id' => $this->session->getAuth()->id,
            'pro_id' => $data['pro_id'] ? intval($data['pro_id']) : null,
            'turn' => $data['turn'] ? intval($data['turn']) : null,
            'amount_end' => $data['amount_end'] ? intval($data['amount_end']) : null,
            'r_date' => $data['r_date']
        ]);
        if(!$dbRTEF->validate()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbRTEF->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $dbReformation = (new Reformation())->get([
            'pro_id' => $dbRTEF->pro_id, 
            'r_date' => $dbRTEF->r_date, 
            'turn' => $dbRTEF->turn
        ])->fetch(false);
        if(!$dbReformation) {
            $dbReformation = new Reformation();
            $dbReformation->usu_id = $dbRTEF->usu_id;
        }
        
        $dbReformation->loadData([
            'pro_id' => $dbRTEF->pro_id,
            'turn' => $dbRTEF->turn, 
            'r_date' => $dbRTEF->r_date,
            'amount_end' => $dbRTEF->amount_end
        ]);
        if(!$dbReformation->save()) {
            $this->setMessage('error', ErrorMessages::form())->setErrors($dbReformation->getFirstErrors())->APIResponse([], 422);
            return;
        }

        $this->setMessage(
            'success', 
            sprintf(
                _('A quantidade de consertados do fim do %sº turno do dia %s para o produto "%s" foi registrado com sucesso!'), 
                $dbReformation->turn, 
                $dbReformation->getReformationDateTime()->format('d/m/Y'), 
                $dbReformation->product()->desc_short
            )
        )->APIResponse([], 200);
    }
}
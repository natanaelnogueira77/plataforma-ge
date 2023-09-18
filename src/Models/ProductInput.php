<?php

namespace Src\Models;

use GTG\MVC\DB\DBModel;
use Src\Models\Product;
use Src\Models\User;

class ProductInput extends DBModel 
{
    const CS_RECEIVED = 1;
    const CS_ORDERED = 2;
    const CS_AWAITING = 3;

    public ?Product $product = null;
    public ?User $user = null;

    public static function tableName(): string 
    {
        return 'produto_entrada';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return [
            'usu_id', 
            'pro_id', 
            'boxes', 
            'units', 
            'street', 
            'position', 
            'height', 
            'c_status'
        ];
    }

    public function rules(): array 
    {
        return [
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'pro_id' => [
                [self::RULE_REQUIRED, 'message' => _('O produto é obrigatório!')]
            ],
            'boxes' => [
                [self::RULE_REQUIRED, 'message' => _('A quantidade de caixas é obrigatória!')]
            ],
            'units' => [
                [self::RULE_REQUIRED, 'message' => _('A quantidade de unidades é obrigatória!')]
            ],
            'c_status' => [
                [self::RULE_REQUIRED, 'message' => _('O status é obrigatório!')],
                [self::RULE_IN, 'values' => array_keys(self::getStates()), 'message' => _('O status é inválido!')]
            ]
        ] + (
            $this->isReceived() 
            ? [
                'street' => [
                    [self::RULE_REQUIRED, 'message' => _('A rua é obrigatória!')]
                ],
                'position' => [
                    [self::RULE_REQUIRED, 'message' => _('A posição é obrigatória!')]
                ],
                'height' => [
                    [self::RULE_REQUIRED, 'message' => _('A altura é obrigatória!')]
                ]
            ] : [] 
        ) + [
            self::RULE_RAW => [
                function ($model) {
                    if(!$model->hasError('pro_id')) {
                        if($stock = Stock::getByProductId($model->pro_id)) {
                            if($model->id) {
                                $boxesAmount = $model->boxes;
                                $unitsAmount = $model->units;
            
                                $oldProductInput = (new self())->findById($model->id);
                                $boxesAmount = $oldProductInput->boxes - $boxesAmount;
                                $unitsAmount = $oldProductInput->units - $unitsAmount;
                                
                                if(!$model->hasError('boxes') && $boxesAmount > $stock->boxes) {
                                    $model->addError('boxes', sprintf(_('O novo número que você determinou removerá mais do que está no estoque!')));
                                }
                        
                                if(!$model->hasError('units') && $unitsAmount > $stock->units) {
                                    $model->addError('units', sprintf(_('O novo número que você determinou removerá mais do que está no estoque!')));
                                }
                            }
                        }
                    }
                }
            ]
        ];
    }

    public function save(): bool 
    {
        $this->street = $this->isReceived() ? $this->street : null;
        $this->position = $this->isReceived() ? $this->position : null;
        $this->height = $this->isReceived() ? $this->height : null;
        return parent::save();
    }

    public static function insertMany(array $objects): array|false 
    {
        if(count($objects) > 0) {
            foreach($objects as $object) {
                if(is_array($object)) $object = (new self())->loadData($object);
            }

            $object->street = $object->isReceived() ? $object->street : null;
            $object->position = $object->isReceived() ? $object->position : null;
            $object->height = $object->isReceived() ? $object->height : null;
        }

        return parent::insertMany($objects);
    }

    public function destroy(): bool 
    {
        if($stock = Stock::getByProductId($this->pro_id)) {
            if(!$this->hasError('boxes') && $this->boxes > $stock->boxes) {
                $this->addError('destroy', sprintf(_('A quantidade de caixas à ser removida é maior do que está no estoque!')));
                return false;
            }
    
            if(!$this->hasError('units') && $this->units > $stock->units) {
                $this->addError('destroy', sprintf(_('A quantidade de unidades à ser removida é maior do que está no estoque!')));
                return false;
            }
        }

        return parent::destroy();
    }

    public function product(string $columns = '*'): ?Product 
    {
        $this->product = $this->belongsTo(Product::class, 'pro_id', 'id', $columns)->fetch(false);
        return $this->product;
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }

    public static function withProduct(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            Product::class, 
            'pro_id', 
            'product', 
            'id', 
            $filters, 
            $columns
        );
    }

    public static function withUser(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            User::class, 
            'usu_id', 
            'user', 
            'id', 
            $filters, 
            $columns
        );
    }

    public static function getStates(): array 
    {
        return [
            self::CS_RECEIVED => _('Recebido'),
            self::CS_ORDERED => _('Pedido Feito'),
            self::CS_AWAITING => _('Aguardando')
        ];
    }

    public function getStatus(): ?string 
    {
        return isset(self::getStates()[$this->c_status]) ? self::getStates()[$this->c_status] : null;
    }

    public function isReceived(): bool 
    {
        return $this->c_status == self::CS_RECEIVED;
    }

    public function isOrdered(): bool 
    {
        return $this->c_status == self::CS_ORDERED;
    }

    public function isAwaiting(): bool 
    {
        return $this->c_status == self::CS_AWAITING;
    }
}
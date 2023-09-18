<?php

namespace Src\Models;

use GTG\MVC\DB\DBModel;
use Src\Models\Collaborator;
use Src\Models\Product;
use Src\Models\Stock;
use Src\Models\User;

class ProductOutput extends DBModel 
{
    public ?Collaborator $collaborator = null;
    public ?Product $product = null;
    public ?User $user = null;

    public static function tableName(): string 
    {
        return 'produto_saida';
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
            'col_id', 
            'boxes', 
            'units'
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
            'col_id' => [
                [self::RULE_REQUIRED, 'message' => _('O colaborador é obrigatório!')]
            ],
            'boxes' => [
                [self::RULE_REQUIRED, 'message' => _('A quantidade de caixas é obrigatória!')]
            ],
            'units' => [
                [self::RULE_REQUIRED, 'message' => _('A quantidade de unidades é obrigatória!')]
            ],
            self::RULE_RAW => [
                function ($model) {
                    if(!$model->hasError('pro_id')) {
                        if($stock = Stock::getByProductId($model->pro_id)) {
                            $boxesAmount = $model->boxes;
                            $unitsAmount = $model->units;
            
                            if($model->id) {
                                $oldProductOutput = (new self())->findById($model->id);
                                $boxesAmount = $boxesAmount - $oldProductOutput->boxes;
                                $unitsAmount = $unitsAmount - $oldProductOutput->units;
                            }
            
                            if(!$model->hasError('boxes') && $boxesAmount > $stock->boxes) {
                                $model->addError('boxes', sprintf(_('O número que você determinou ultrapassa o que está no estoque!')));
                            }
                    
                            if(!$model->hasError('units') && $unitsAmount > $stock->units) {
                                $model->addError('units', sprintf(_('O número que você determinou ultrapassa o que está no estoque!')));
                            }
                        } else {
                            $model->addError('boxes', sprintf(_('O número que você determinou ultrapassa o que está no estoque!')));
                            $model->addError('units', sprintf(_('O número que você determinou ultrapassa o que está no estoque!')));
                        }
                    }
                }
            ]
        ];
    }

    public function collaborator(string $columns = '*'): ?Collaborator 
    {
        $this->collaborator = $this->belongsTo(Collaborator::class, 'col_id', 'id', $columns)->fetch(false);
        return $this->collaborator;
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

    public static function withCollaborator(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo(
            $objects, 
            Collaborator::class, 
            'col_id', 
            'collaborator', 
            'id', 
            $filters, 
            $columns
        );
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
}
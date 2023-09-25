<?php

namespace Src\Models;

use DateTime;
use GTG\MVC\DB\DBModel;
use Src\Models\Product;

class Reformation extends DBModel 
{
    public ?Product $product = null;

    public static function tableName(): string 
    {
        return 'reformacao';
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
            'amount_start', 
            'amount_end', 
            'turn', 
            'r_date'
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
            'turn' => [
                [self::RULE_REQUIRED, 'message' => _('O turno é obrigatório!')]
            ],
            'r_date' => [
                [self::RULE_REQUIRED, 'message' => _('A data é obrigatória!')],
                [self::RULE_DATETIME, 'pattern' => 'Y-m-d', 'message' => _('A data deve seguir o padrão dd/mm/aaaa!')]
            ],
            self::RULE_RAW => [
                function ($model) {
                    if(!$model->hasError('pro_id')) {
                        if($reformationStock = ReformationStock::getByProductId($model->pro_id)) {
                            $amountStart = $model->amount_start;
                            $amountEnd = $model->amount_end;
            
                            if($model->id) {
                                $oldReformation = (new self())->findById($model->id);
                                $amountStart = $oldReformation->amount_start - $amountStart;
                                $amountEnd = $amountEnd - $oldReformation->amount_end;
                                
                                if(!$model->hasError('amount_start') && $amountStart > $reformationStock->amount) {
                                    $model->addError('amount_start', sprintf(_('O novo número que você determinou removerá mais do que está no estoque!')));
                                }
                            }
            
                            if(!$model->hasError('amount_end') && $amountEnd > $reformationStock->amount) {
                                $model->addError('amount_end', sprintf(_('O número que você determinou ultrapassa o que está no estoque!')));
                            }
                        } else {
                            if($model->amount_end) {
                                $model->addError('amount_end', sprintf(_('O número que você determinou ultrapassa o que está no estoque!')));
                            }
                        }
                    }
            
                    if(!$model->hasError('turn')) {
                        if($model->turn > 3) {
                            $model->addError('turn', sprintf(_('O turno deve ser menor ou igual à %s!'), 3));
                        } elseif($model->turn > 1 && !(new self())->get(['r_date' => $model->r_date, 'pro_id' => $model->pro_id, 'turn' => $model->turn - 1])->count()) {
                            $model->addError('turn', sprintf(_('As quantidades do %sº turno precisam ser informadas!'), $model->turn - 1));
                        }
                    }
                }
            ]
        ];
    }

    public function save(): bool 
    {
        $this->amount_start = $this->amount_start ? $this->amount_start : 0;
        $this->amount_end = $this->amount_end ? $this->amount_end : 0;

        return parent::save();
    }

    public function destroy(): bool 
    {
        if($reformationStock = ReformationStock::getByProductId($this->pro_id)) {
            if(!$this->hasError('amount_start') && $this->amount_start > $reformationStock->amount) {
                $this->addError('destroy', sprintf(_('A quantidade consertada é maior do que está no estoque!')));
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

    public static function getByProductId(int $productId, string $columns = '*'): ?self 
    {
        return (new self())->get(['pro_id' => $productId], $columns)->fetch(false);
    }

    public function getReformationDateTime(): DateTime 
    {
        return new DateTime($this->r_date);
    }

    public function getBalance(): int 
    {
        return $this->amount_start - $this->amount_end;
    }

    public function getCreatedAtDateTime(): DateTime 
    {
        return new DateTime($this->created_at);
    }

    public function getUpdatedAtDateTime(): DateTime 
    {
        return new DateTime($this->updated_at);
    }
}
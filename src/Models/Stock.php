<?php

namespace Src\Models;

use GTG\MVC\DB\DBModel;
use Src\Models\Product;
use Src\Models\ProductInput;

class Stock extends DBModel 
{
    public $product;

    public static function tableName(): string 
    {
        return 'estoque';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return ['pro_id', 'boxes', 'units'];
    }

    public function rules(): array 
    {
        return [
            'pro_id' => [
                [self::RULE_REQUIRED, 'message' => _('O produto é obrigatório!')]
            ],
            'boxes' => [
                [self::RULE_REQUIRED, 'message' => _('A quantidade de caixas é obrigatória!')]
            ],
            'units' => [
                [self::RULE_REQUIRED, 'message' => _('A quantidade de unidades é obrigatória!')]
            ]
        ];
    }

    public function destroy(): bool 
    {
        return parent::destroy();
    }

    public function product(string $columns = '*'): ?Product 
    {
        $this->product = $this->belongsTo(Product::class, 'pro_id', 'id', $columns)->fetch(false);
        return $this->product;
    }

    public static function withProduct(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo($objects, Product::class, 'pro_id', 'product', 'id', $filters, $columns);
    }

    public static function getByProductId(int $productId, string $columns = '*'): ?self 
    {
        return (new self())->get(['pro_id' => $productId], $columns)->fetch(false);
    }
}
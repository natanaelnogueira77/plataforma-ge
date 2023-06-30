<?php

namespace Src\Models;

use GTG\MVC\DB\DBModel;
use Src\Models\ProductInput;
use Src\Models\ProductOutput;
use Src\Models\User;

class Product extends DBModel 
{
    public $productInputs = [];
    public $productOutputs = [];
    public $user;

    public static function tableName(): string 
    {
        return 'produto';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return ['usu_id', 'desc_short'];
    }

    public function rules(): array 
    {
        return [
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'desc_short' => [
                [self::RULE_REQUIRED, 'message' => _('A descrição é obrigatória!')],
                [self::RULE_MAX, 'max' => 300, 'message' => sprintf(_('A descrição deve conter no máximo %s caractéres!'), 300)]
            ]
        ];
    }

    public function destroy(): bool 
    {
        return parent::destroy();
    }

    public function productInputs(array $filters = [], string $columns = '*'): ?array 
    {
        $this->productInputs = $this->hasMany(ProductInput::class, 'pro_id', 'id', $filters, $columns)->fetch(true);
        return $this->productInputs;
    }

    public function productOutputs(array $filters = [], string $columns = '*'): ?array 
    {
        $this->productOutputs = $this->hasMany(ProductOutput::class, 'pro_id', 'id', $filters, $columns)->fetch(true);
        return $this->productOutputs;
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }

    public static function withProductInputs(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withHasMany($objects, ProductInput::class, 'pro_id', 'productInput', 'id', $filters, $columns);
    }

    public static function withProductOutputs(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withHasMany($objects, ProductOutput::class, 'pro_id', 'productOutput', 'id', $filters, $columns);
    }

    public static function withUser(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo($objects, User::class, 'usu_id', 'user', 'id', $filters, $columns);
    }
}
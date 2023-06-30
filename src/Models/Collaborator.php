<?php

namespace Src\Models;

use GTG\MVC\DB\DBModel;
use Src\Models\User;

class Collaborator extends DBModel 
{
    public $user;
    
    public static function tableName(): string 
    {
        return 'colaborador';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return ['usu_id', 'name'];
    }

    public function rules(): array 
    {
        return [
            'usu_id' => [
                [self::RULE_REQUIRED, 'message' => _('O usuário é obrigatório!')]
            ],
            'name' => [
                [self::RULE_REQUIRED, 'message' => _('O nome é obrigatório!')],
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O nome deve conter no máximo %s caractéres!'), 100)]
            ]
        ];
    }

    public function destroy(): bool 
    {
        if((new ProductOutput())->get(['col_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir um colaborador vinculado à uma saída de produto!'));
            return false;
        }
        return parent::destroy();
    }

    public function user(string $columns = '*'): ?User 
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }

    public static function withUser(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo($objects, User::class, 'utip_id', 'user', 'id', $filters, $columns);
    }

    public static function getByUserId(string $userId, string $columns = '*'): ?self 
    {
        return (new self())->get(['usu_id' => $userId], $columns)->fetch(true);
    }
}
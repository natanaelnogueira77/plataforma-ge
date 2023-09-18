<?php

namespace Src\Models;

use DateTime;
use GTG\MVC\DB\DBModel;
use Src\Models\User;

class UserMeta extends DBModel 
{
    const KEY_LANG = 'lang';
    const KEY_LAST_PASS_REQUEST = 'last_pass_request';

    public ?User $user = null;

    public static function tableName(): string 
    {
        return 'usuario_meta';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return [
            'usu_id', 
            'meta', 
            'value'
        ];
    }

    public function rules(): array 
    {
        return [
            'meta' => [
                [self::RULE_REQUIRED, 'message' => _('O metadado é obrigatório!')],
                [self::RULE_MAX, 'max' => 50, 'message' => sprintf(_('O metadado deve conter no máximo %s caractéres!'), 50)]
            ],
            self::RULE_RAW => [
                function ($model) {
                    if(!$model->hasError('meta')) {
                        if($model->meta == self::KEY_LANG) {
                            if(!$model->value) {
                                $model->addError(self::KEY_LANG, _('A linguagem é obrigatória!'));
                            }
                        } elseif($model->meta == self::KEY_LAST_PASS_REQUEST) {
                            if(!$model->value) {
                                $model->addError(self::KEY_LAST_PASS_REQUEST, _('A data da última alteração de senha é obrigatória!'));
                            } elseif(!DateTime::createFromFormat('Y-m-d H:i:s', $model->value)) {
                                $model->addError(self::KEY_LAST_PASS_REQUEST, _('A data da última alteração de senha deve seguir o padrão dd/mm/aaaa hh:mm:ss!'));
                            }
                        }
                    }
                }
            ]
        ];
    }

    public function user(string $columns = '*'): ?User
    {
        $this->user = $this->belongsTo(User::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->user;
    }
}
<?php

namespace Src\Models;

use GTG\MVC\Model;
use Src\Models\User;

class UserForm extends Model 
{
    public $id = null;
    public $utip_id = 0;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirm = '';
    public $update_password = '';
    public $slug = '';

    public function rules(): array 
    {
        return [
            'utip_id' => [
                [self::RULE_REQUIRED, 'message' => _('O tipo de usuário é obrigatório!')]
            ],
            'name' => [
                [self::RULE_REQUIRED, 'message' => _('O nome é obrigatório!')],
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O nome deve conter no máximo %s caractéres!'), 100)]
            ],
            'email' => [
                [self::RULE_REQUIRED, 'message' => _('O email é obrigatório!')], 
                [self::RULE_EMAIL, 'message' => _('O email é inválido!')], 
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O email deve conter no máximo %s caractéres!'), 100)]
            ]
        ] + (
            ($this->id && $this->update_password) || !$this->id 
            ? [
                'password' => [
                    [self::RULE_REQUIRED, 'message' => _('A senha é obrigatória!')], 
                    [self::RULE_MIN, 'min' => 5, 'message' => sprintf(_('A senha deve conter no mínimo %s caractéres!'), 5)]
                ],
                'password_confirm' => [
                    [self::RULE_REQUIRED, 'message' => _('A confirmação de senha é obrigatória!')], 
                    [self::RULE_MATCH, 'match' => 'password', 'message' => _('As senhas não correspondem!')]
                ]
            ] : []
        ) + [
            'slug' => [
                [self::RULE_REQUIRED, 'message' => _('O apelido é obrigatório!')],
                [self::RULE_MAX, 'max' => 100, 'message' => sprintf(_('O apelido deve conter no máximo %s caractéres!'), 100)]
            ]
        ];
    }

    public function validate(): bool 
    {
        parent::validate();

        if(!$this->hasError('email')) {
            if((new User())->get(['email' => $this->email] + (isset($this->id) ? ['!=' => ['id' => $this->id]] : []))->count()) {
                $this->addError('email', _('O email informado já está em uso! Tente outro.'));
            }
        }
        
        if(!$this->hasError('slug')) {
            if((new User())->get(['slug' => $this->slug] + (isset($this->id) ? ['!=' => ['id' => $this->id]] : []))->count()) {
                $this->addError('slug', _('O apelido informado já está em uso! Tente outro.'));
            }
        }

        return !$this->hasErrors();
    }
}
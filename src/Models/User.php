<?php

namespace Src\Models;

use GTG\MVC\DB\UserModel;
use Src\Models\SocialUser;
use Src\Models\UserMeta;
use Src\Models\UserType;

class User extends UserModel 
{
    const UT_ADMIN = 1;
    const UT_LEADER = 2;
    const UT_OPERATOR = 3;

    public $socialUser;
    public $userMetas = [];
    public $userType;
    
    public static function tableName(): string 
    {
        return 'usuario';
    }

    public static function primaryKey(): string 
    {
        return 'id';
    }

    public static function attributes(): array 
    {
        return ['utip_id', 'name', 'email', 'password', 'token', 'slug'];
    }

    public static function metaTableData(): ?array 
    {
        return [
            'class' => UserMeta::class,
            'entity' => 'usu_id',
            'meta' => 'meta',
            'value' => 'value'
        ];
    }

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
            ],
            'password' => [
                [self::RULE_REQUIRED, 'message' => _('A senha é obrigatória!')], 
                [self::RULE_MIN, 'min' => 5, 'message' => sprintf(_('A senha deve conter no mínimo %s caractéres!'), 5)]
            ],
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
            if((new self())->get(['email' => $this->email] + (isset($this->id) ? ['!=' => ['id' => $this->id]] : []))->count()) {
                $this->addError('email', _('O email informado já está em uso! Tente outro.'));
            }
        }
        
        if(!$this->hasError('slug')) {
            if((new self())->get(['slug' => $this->slug] + (isset($this->id) ? ['!=' => ['id' => $this->id]] : []))->count()) {
                $this->addError('slug', _('O apelido informado já está em uso! Tente outro.'));
            }
        }

        return !$this->hasErrors();
    }

    public function save(): bool 
    {
        $this->slug = $this->slug ? slugify($this->slug) : ($this->name ? slugify($this->name) : null);
        $this->email = strtolower($this->email);
        $this->token = is_string($this->email) ? md5($this->email) : null;

        return parent::save();
    }

    public static function insertMany(array $objects): array|false 
    {
        if(count($objects) > 0) {
            foreach($objects as $object) {
                if(is_array($object)) $object = (new self())->loadData($object);
                $object->slug = $object->slug ? slugify($object->slug) : ($object->name ? slugify($object->name) : null);
                $object->email = strtolower($object->email);
                $object->token = is_string($object->email) ? md5($object->email) : null;
            }
        }

        return parent::insertMany($objects);
    }

    public function encode(): static 
    {
        if(!password_get_info($this->password)['algo']) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }

        return $this;
    }

    public function destroy(): bool 
    {
        if($this->isAdmin()) {
            $this->addError('destroy', _('Vai por mim, isso vai dar ruim! Você não pode excluir o administrador do sistema.'));
            return false;
        } elseif((new SocialUser())->get(['usu_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir um usuário vinculado à uma rede social!'));
            return false;
        } elseif((new UserMeta())->get(['usu_id' => $this->id])->count()) {
            $this->addError('destroy', _('Você não pode excluir um usuário com dados armazenados!'));
            return false;
        }
        return parent::destroy();
    }

    public function socialUser(string $columns = '*'): ?SocialUser 
    {
        $this->socialUser = $this->hasOne(SocialUser::class, 'usu_id', 'id', $columns)->fetch(false);
        return $this->socialUser;
    }

    public function userMetas(array $filters = [], string $columns = '*'): ?array 
    {
        $this->userMetas = $this->hasMany(UserMeta::class, 'usu_id', 'id', $filters, $columns)->fetch(true);
        return $this->userMetas;
    }

    public function userType(string $columns = '*'): ?UserType 
    {
        $this->userType = $this->belongsTo(UserType::class, 'utip_id', 'id', $columns)->fetch(false);
        return $this->userType;
    }

    public static function withSocialUser(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withHasOne($objects, UserType::class, 'usu_id', 'socialUser', 'id', $filters, $columns);
    }

    public static function withUserType(array $objects, array $filters = [], string $columns = '*'): array
    {
        return self::withBelongsTo($objects, UserType::class, 'utip_id', 'userType', 'id', $filters, $columns);
    }

    public function isAdmin(): bool
    {
        return $this->utip_id == self::UT_ADMIN;
    }

    public function isLeader(): bool
    {
        return $this->utip_id == self::UT_LEADER;
    }

    public function isOperator(): bool
    {
        return $this->utip_id == self::UT_OPERATOR;
    }

    public static function getBySlug(string $slug, string $columns = '*'): ?self 
    {
        return (new self())->get(['slug' => $slug], $columns)->fetch(false);
    }

    public static function getByEmail(string $email, string $columns = '*'): ?self 
    {
        return (new self())->get(['email' => $email], $columns)->fetch(false);
    }

    public static function getByToken(string $token, string $columns = '*'): ?self 
    {
        return (new self())->get(['token' => $token], $columns)->fetch(false);
    }

    public function verifyPassword(string $password): bool 
    {
        return $this->password ? password_verify($password, $this->password) : false;
    }
}
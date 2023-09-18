<?php

namespace Src\Models;

use GTG\MVC\Model;

class ReformationTurnStartForm extends Model 
{
    public ?int $usu_id = null;
    public ?int $pro_id = null;
    public ?int $turn = null;
    public ?int $amount_start = null;
    public ?string $r_date = null;

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
            ]
        ];
    }
}
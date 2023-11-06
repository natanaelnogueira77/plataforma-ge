<?php 

namespace Src\Components;

class DateUtils 
{
    public static function getMonths(): array 
    {
        return [
            1 => _('Janeiro'),
            2 => _('Fevereiro'),
            3 => _('Março'),
            4 => _('Abril'),
            5 => _('Maio'),
            6 => _('Junho'),
            7 => _('Julho'),
            8 => _('Agosto'),
            9 => _('Setembro'),
            10 => _('Outubro'),
            11 => _('Novembro'),
            12 => _('Dezembro')
        ];
    }
    
    public static function getWeekdays(): array 
    {
        return [
            0 => _('Domingo'),
            1 => _('Segunda-feira'),
            2 => _('Terça-feira'),
            3 => _('Quarta-feira'),
            4 => _('Quinta-feira'),
            5 => _('Sexta-feira'),
            6 => _('Sábado')
        ];
    }
}
<?php 

namespace Src\Database\Seeders;

use GTG\MVC\DB\Seeder;
use Src\Models\Product;

class ProductSeeder extends Seeder 
{
    public function run(): void 
    {
        Product::insertMany([
            ['usu_id' => 1, 'desc_short' => 'FIR 52 SEM RODA (EIXO FIXO ROLLTAINERS SECO)'],
            ['usu_id' => 1, 'desc_short' => 'GIR 52 SEM RODA (EIXO GIRATORIO)'],
            ['usu_id' => 1, 'desc_short' => 'GLR 414 SEM RODA COM EIXO (EIXO FIXO ISOTÉRMICO)'],
            ['usu_id' => 1, 'desc_short' => 'R 414 NZL RODA GLR 414 (RODA DE ISOTÉRMICO)'],
            ['usu_id' => 1, 'desc_short' => 'FLR 414 SEM RODA COM EIXO 250KG (EIXO GIRATÓRIO ISOTÉRMICO)'],
            ['usu_id' => 1, 'desc_short' => 'GIR 52 EIXO GIRATÓRIO (EIXO GIRATÓRIO ROLLT SECO)'],
            ['usu_id' => 1, 'desc_short' => 'FIR 52 EIXO FIXO (ROLLTAINER SECO)'],
            ['usu_id' => 1, 'desc_short' => 'R 52 NPL RODA (ROLLTAINER SECO)'],
            ['usu_id' => 1, 'desc_short' => 'PARAFUSO M8 1.25MA x 20 P/SEXTAVADO 8.8 ZB (ROLL ISOTÉRMICO)'],
            ['usu_id' => 1, 'desc_short' => 'ARRUELA LISA M10 AÇO ZINCADO BRANCO (ROLL ISOTÉRMICO)'],
            ['usu_id' => 1, 'desc_short' => 'ARRUELA LISA 3/8 AÇO ZINCADO BRANCO (ROLL ISOTÉRMICO)'],
            ['usu_id' => 1, 'desc_short' => 'PARAFUSO SEXTAVADO ROSCA INTEIRA 3/8 16X 3X4 UNC AÇO (ROLL SECO)']
        ]);
    }
}
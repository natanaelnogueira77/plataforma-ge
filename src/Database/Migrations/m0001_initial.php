<?php 

namespace Src\Database\Migrations;

use GTG\MVC\DB\Migration;

class m0001_initial extends Migration 
{
    public function up(): void
    {
        $this->db->createTable('colaborador', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('name', 100);
            $table->timestamps();
        });

        $this->db->createTable('config', function ($table) {
            $table->id();
            $table->string('meta', 50);
            $table->text('value')->nullable();
        });

        $this->db->createTable('estoque', function ($table) {
            $table->id();
            $table->integer('pro_id');
            $table->integer('boxes');
            $table->integer('units');
            $table->timestamps();
        });

        $this->db->createTable('produto', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('desc_short', 300);
            $table->timestamps();
        });

        $this->db->createTable('produto_entrada', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('pro_id');
            $table->integer('boxes');
            $table->integer('units');
            $table->integer('c_status');
            $table->integer('street')->nullable();
            $table->integer('position')->nullable();
            $table->integer('height')->nullable();
            $table->timestamps();
        });

        $this->db->createTable('produto_saida', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('pro_id');
            $table->integer('col_id');
            $table->integer('boxes');
            $table->integer('units');
            $table->timestamps();
        });

        $this->db->createTable('social_usuario', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('social_id', 255)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('social', 100)->nullable();
            $table->timestamps();
        });

        $this->db->createTable('usuario', function ($table) {
            $table->id();
            $table->integer('utip_id');
            $table->string('name', 50);
            $table->string('email', 100);
            $table->string('password', 100);
            $table->string('token', 100);
            $table->string('slug', 100);
            $table->timestamps();
        });

        $this->db->createTable('usuario_meta', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->string('meta', 50);
            $table->text('value')->nullable();
        });

        $this->db->createTable('usuario_tipo', function ($table) {
            $table->id();
            $table->string('name_sing', 50);
            $table->string('name_plur', 50);
            $table->timestamps();
        });

        $this->exec("
            CREATE PROCEDURE `SP_UpdateStock`(`id_prod` int, `boxes_add` int, `units_add` int)
            BEGIN
                declare counter int(11);
            
                SELECT count(*) into counter FROM estoque WHERE pro_id = id_prod;
            
                IF counter > 0 THEN
                    UPDATE estoque SET boxes = boxes + boxes_add, units = units + units_add
                    WHERE pro_id = id_prod;
                ELSE
                    INSERT INTO estoque (pro_id, boxes, units) values (id_prod, boxes_add, units_add);
                END IF;
            END;
        ");
        
        $this->exec("
            CREATE TRIGGER `TRG_ProductInput_AI` AFTER INSERT ON `produto_entrada`
            FOR EACH ROW
            BEGIN
                IF new.c_status = 1 THEN 
                    CALL SP_UpdateStock (new.pro_id, new.boxes, new.units);
                END IF;
            END;
        ");
        
        $this->exec("
            CREATE TRIGGER `TRG_ProductInput_AU` AFTER UPDATE ON `produto_entrada`
            FOR EACH ROW
            BEGIN 
                IF old.c_status = 1 AND new.c_status = 1 THEN 
                    CALL SP_UpdateStock (new.pro_id, new.boxes - old.boxes, new.units - old.units);
                ELSEIF old.c_status != 1 AND new.c_status = 1 THEN 
                    CALL SP_UpdateStock (new.pro_id, new.boxes, new.units);
                ELSEIF old.c_status = 1 AND new.c_status != 1 THEN 
                    CALL SP_UpdateStock (new.pro_id, old.boxes * -1, old.units * -1); 
                END IF;
            END;
        ");
        
        $this->exec("
            CREATE TRIGGER `TRG_ProductInput_AD` AFTER DELETE ON `produto_entrada`
            FOR EACH ROW
            BEGIN 
                IF old.c_status = 1 THEN 
                    CALL SP_UpdateStock (old.pro_id, old.boxes * -1, old.units * -1); 
                END IF;
            END;
        ");

        $this->exec("
            CREATE TRIGGER `TRG_ProductOutput_AI` AFTER INSERT ON `produto_saida`
            FOR EACH ROW
            BEGIN
                CALL SP_UpdateStock (new.pro_id, new.boxes * -1, new.units * -1);
            END;
        ");
        
        $this->exec("
            CREATE TRIGGER `TRG_ProductOutput_AU` AFTER UPDATE ON `produto_saida`
            FOR EACH ROW
            BEGIN
                CALL SP_UpdateStock (new.pro_id, old.boxes - new.boxes, old.units - new.units);
            END;
        ");

        $this->exec("
            CREATE TRIGGER `TRG_ProductOutput_AD` AFTER DELETE ON `produto_saida`
            FOR EACH ROW
            BEGIN
                CALL SP_UpdateStock (old.pro_id, old.boxes, old.units);
            END;
        ");
    }

    public function down(): void
    {
        $this->exec('DROP TRIGGER IF EXISTS TRG_ProductInput_AI');
        $this->exec('DROP TRIGGER IF EXISTS TRG_ProductInput_AU');
        $this->exec('DROP TRIGGER IF EXISTS TRG_ProductInput_AD');
        $this->exec('DROP TRIGGER IF EXISTS TRG_ProductOutput_AI');
        $this->exec('DROP TRIGGER IF EXISTS TRG_ProductOutput_AU');
        $this->exec('DROP TRIGGER IF EXISTS TRG_ProductOutput_AD');
        $this->exec('DROP PROCEDURE IF EXISTS SP_UpdateStock');
        $this->db->dropTableIfExists('colaborador');
        $this->db->dropTableIfExists('config');
        $this->db->dropTableIfExists('estoque');
        $this->db->dropTableIfExists('produto');
        $this->db->dropTableIfExists('produto_entrada');
        $this->db->dropTableIfExists('produto_saida');
        $this->db->dropTableIfExists('social_usuario');
        $this->db->dropTableIfExists('usuario');
        $this->db->dropTableIfExists('usuario_meta');
        $this->db->dropTableIfExists('usuario_tipo');
    }
}
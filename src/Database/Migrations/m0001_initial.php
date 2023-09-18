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

        $this->db->createProcedure('SP_UpdateStock', function ($procedure) {
            $procedure->integer('id_prod');
            $procedure->integer('boxes_add');
            $procedure->integer('units_add');
            $procedure->statement("
                DECLARE estoque_id int(11);
                
                SELECT id into estoque_id FROM estoque WHERE pro_id = id_prod LIMIT 1;
            
                IF estoque_id > 0 THEN
                    UPDATE estoque SET boxes = boxes + boxes_add, units = units + units_add
                    WHERE id = estoque_id;
                ELSE
                    INSERT INTO estoque (pro_id, boxes, units) VALUES (id_prod, boxes_add, units_add);
                END IF;
            ");
        });
        
        $this->db->createTrigger('TRG_ProductInput_AI', function ($trigger) {
            $trigger->event('AFTER INSERT ON `produto_entrada` FOR EACH ROW');
            $trigger->statement("
                IF new.c_status = 1 THEN 
                    CALL SP_UpdateStock (new.pro_id, new.boxes, new.units);
                END IF;
            ");
        });
        
        $this->db->createTrigger('TRG_ProductInput_AU', function ($trigger) {
            $trigger->event('AFTER UPDATE ON `produto_entrada` FOR EACH ROW');
            $trigger->statement("
                IF old.c_status = 1 AND new.c_status = 1 THEN 
                    CALL SP_UpdateStock (new.pro_id, new.boxes - old.boxes, new.units - old.units);
                ELSEIF old.c_status != 1 AND new.c_status = 1 THEN 
                    CALL SP_UpdateStock (new.pro_id, new.boxes, new.units);
                ELSEIF old.c_status = 1 AND new.c_status != 1 THEN 
                    CALL SP_UpdateStock (new.pro_id, old.boxes * -1, old.units * -1); 
                END IF;
            ");
        });
        
        $this->db->createTrigger('TRG_ProductInput_AD', function ($trigger) {
            $trigger->event('AFTER DELETE ON `produto_entrada` FOR EACH ROW');
            $trigger->statement("
                IF old.c_status = 1 THEN 
                    CALL SP_UpdateStock (old.pro_id, old.boxes * -1, old.units * -1); 
                END IF;
            ");
        });

        $this->db->createTrigger('TRG_ProductOutput_AI', function ($trigger) {
            $trigger->event('AFTER INSERT ON `produto_saida` FOR EACH ROW');
            $trigger->statement("CALL SP_UpdateStock (new.pro_id, new.boxes * -1, new.units * -1);");
        });
        
        $this->db->createTrigger('TRG_ProductOutput_AU', function ($trigger) {
            $trigger->event('AFTER UPDATE ON `produto_saida` FOR EACH ROW');
            $trigger->statement("CALL SP_UpdateStock (new.pro_id, old.boxes - new.boxes, old.units - new.units);");
        });

        $this->db->createTrigger('TRG_ProductOutput_AD', function ($trigger) {
            $trigger->event('AFTER DELETE ON `produto_saida` FOR EACH ROW');
            $trigger->statement("CALL SP_UpdateStock (old.pro_id, old.boxes, old.units);");
        });
    }

    public function down(): void
    {
        $this->db->dropTriggerIfExists('TRG_ProductInput_AI');
        $this->db->dropTriggerIfExists('TRG_ProductInput_AU');
        $this->db->dropTriggerIfExists('TRG_ProductInput_AD');
        $this->db->dropTriggerIfExists('TRG_ProductOutput_AI');
        $this->db->dropTriggerIfExists('TRG_ProductOutput_AU');
        $this->db->dropTriggerIfExists('TRG_ProductOutput_AD');
        $this->db->dropProcedureIfExists('SP_UpdateStock');
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
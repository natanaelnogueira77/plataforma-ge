<?php 

namespace Src\Database\Migrations;

use GTG\MVC\DB\Migration;

class m0002_v1_0_1 extends Migration 
{
    public function up(): void
    {
        $this->db->createTable('reformacao', function ($table) {
            $table->id();
            $table->integer('usu_id');
            $table->integer('pro_id');
            $table->integer('amount_start')->nullable();
            $table->integer('amount_end')->nullable();
            $table->integer('turn');
            $table->date('r_date');
            $table->timestamps();
        });
        
        $this->db->createTable('reformacao_estoque', function ($table) {
            $table->id();
            $table->integer('pro_id');
            $table->integer('amount');
            $table->timestamps();
        });

        $this->db->createProcedure('SP_UpdateReformationStock', function ($procedure) {
            $procedure->integer('id_prod');
            $procedure->integer('amount_add');
            $procedure->statement("
                DECLARE reformacao_estoque_id int(11);
            
                SELECT id into reformacao_estoque_id FROM reformacao_estoque WHERE pro_id = id_prod LIMIT 1;
            
                IF reformacao_estoque_id THEN
                    UPDATE reformacao_estoque SET amount = amount + amount_add 
                    WHERE id = reformacao_estoque_id;
                ELSE
                    INSERT INTO reformacao_estoque (pro_id, amount) VALUES (id_prod, amount_add);
                END IF;
            ");
        });
        
        $this->db->createTrigger('TRG_Reformation_AI', function ($trigger) {
            $trigger->event('AFTER INSERT ON `reformacao` FOR EACH ROW');
            $trigger->statement("
                CALL SP_UpdateReformationStock (new.pro_id, new.amount_start);
                CALL SP_UpdateReformationStock (new.pro_id, new.amount_end * -1);
            ");
        });
        
        $this->db->createTrigger('TRG_Reformation_AU', function ($trigger) {
            $trigger->event('AFTER UPDATE ON `reformacao` FOR EACH ROW');
            $trigger->statement("
                CALL SP_UpdateReformationStock (new.pro_id, new.amount_start - old.amount_start);
                CALL SP_UpdateReformationStock (new.pro_id, old.amount_end - new.amount_end);
            ");
        });
        
        $this->db->createTrigger('TRG_Reformation_AD', function ($trigger) {
            $trigger->event('AFTER DELETE ON `reformacao` FOR EACH ROW');
            $trigger->statement("
                CALL SP_UpdateReformationStock (old.pro_id, old.amount_start * -1);
                CALL SP_UpdateReformationStock (old.pro_id, old.amount_end);
            ");
        });
    }

    public function down(): void
    {
        $this->db->dropTriggerIfExists('TRG_Reformation_AI');
        $this->db->dropTriggerIfExists('TRG_Reformation_AU');
        $this->db->dropTriggerIfExists('TRG_Reformation_AD');
        $this->db->dropProcedureIfExists('SP_UpdateReformationStock');
        $this->db->dropTableIfExists('reformacao');
        $this->db->dropTableIfExists('reformacao_estoque');
    }
}
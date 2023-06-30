<?php 

namespace GTG\MVC\DB;

use CoffeeCode\DataLayer\Connect;
use GTG\MVC\Application;
use GTG\MVC\DB\Schema\Table;
use PDO;
use PDOStatement;

class Database 
{
    private array $dbInfo = [];
    private array $migrations;
    private array $seeders;

    public function __construct(array $config) 
    {
        $this->dbInfo = $config['pdo'];
        $this->migrations = $config['migrations'];
        $this->seeders = $config['seeders'];
    }

    public function exec(string $sql): int 
    {
        $pdo = Connect::getInstance($this->dbInfo);
        return $pdo->exec($sql);
    }

    public function applyMigrations(): void 
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];
        $files = array_map(
            fn($m) => pathinfo($m, PATHINFO_FILENAME), 
            array_filter(scandir(Application::$ROOT_DIR . '/' . $this->migrations['path']), fn($m) => $m !== '.' && $m !== '..')
        );
        $toApplyMigrations = array_diff($files, $appliedMigrations);
        foreach($toApplyMigrations as $migration) {
            $className = $this->migrations['namespace'] . "\\{$migration}";
            $instance = new $className();
            $this->log("Applying migration {$migration}...");
            $instance->up();
            $this->log("Migration {$migration} applied!");
            $newMigrations[] = $migration;
        }

        if(!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        }
        $this->log('All migrations were applied!');
    }

    public function reverseMigrations(): void 
    {
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $files = array_map(
            fn($m) => pathinfo($m, PATHINFO_FILENAME), 
            array_filter(scandir(Application::$ROOT_DIR . '/' . $this->migrations['path']), fn($m) => $m !== '.' && $m !== '..')
        );
        $toReverseMigrations = array_reverse($appliedMigrations);
        foreach($toReverseMigrations as $migration) {
            $className = $this->migrations['namespace'] . "\\{$migration}";
            $instance = new $className();
            $this->log("Reversing migration {$migration}...");
            $instance->down();
            $this->log("Migration {$migration} reversed!");
            $newMigrations[] = $migration;
        }

        if(!empty($newMigrations)) {
            $this->deleteMigrations();
        }
        $this->log('All migrations were reversed!');
    }

    public function createMigrationsTable(): void 
    {
        $this->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT(1) AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;
        ");
    }

    public function getAppliedMigrations(): array 
    {
        $statement = $this->prepare("SELECT migration FROM migrations");
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations): void 
    {
        $str = implode(',', array_map(fn($m) => "('{$m}')", $migrations));
        $statement = $this->prepare("INSERT INTO migrations (migration) VALUES {$str}");
        $statement->execute();
    }

    public function deleteMigrations(): void 
    {
        $statement = $this->prepare("DELETE FROM migrations WHERE id >= 1");
        $statement->execute();
    }

    public function applySeeders(): void 
    {
        $files = array_map(
            fn($m) => pathinfo($m, PATHINFO_FILENAME), 
            array_filter(scandir(Application::$ROOT_DIR . '/' . $this->seeders['path']), fn($m) => $m !== '.' && $m !== '..')
        );
        foreach($files as $seeder) {
            $className = $this->seeders['namespace'] . "\\{$seeder}";
            $instance = new $className();
            $this->log("Applying seeder {$seeder}...");
            $instance->run();
            $this->log("Seeder {$seeder} applied!");
        }

        $this->log('All seeders were applied!');
    }

    public function createTable(string $tableName, callable $callback): int 
    {
        $table = new Table($tableName);
        $table->create();
        $callback($table);
        return $this->exec($table->build());
    }

    public function alterTable(string $tableName, callable $callback): int 
    {
        $table = new Table($tableName);
        $table->alter();
        $callback($table);
        return $this->exec($table->build());
    }

    public function dropTable(string $tableName): int 
    {
        $table = new Table($tableName);
        $table->drop();
        return $this->exec($table->build());
    }

    public function dropTableIfExists(string $tableName): int 
    {
        $table = new Table($tableName);
        $table->dropIfExists();
        return $this->exec($table->build());
    }

    public function prepare(string $sql): PDOStatement|false
    {
        $pdo = Connect::getInstance($this->dbInfo);
        return $pdo->prepare($sql);
    }

    protected function log(string $message): void 
    {
        echo '[' . date('Y-m-d H:i:s') . '] - ' . $message . PHP_EOL;
    }
}
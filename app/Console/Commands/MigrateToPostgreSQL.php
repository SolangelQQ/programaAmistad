<?php
// Crear archivo: app/Console/Commands/MigrateToPostgreSQL.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateToPostgreSQL extends Command
{
    protected $signature = 'migrate:to-postgresql';
    protected $description = 'Migra datos de MySQL a PostgreSQL';

    public function handle()
    {
        $this->info('Iniciando migración de MySQL a PostgreSQL...');

        // Obtener todas las tablas de MySQL
        $mysqlTables = DB::connection('mysql')->select('SHOW TABLES');
        $mysqlTableColumn = 'Tables_in_' . env('DB_DATABASE');

        foreach ($mysqlTables as $table) {
            $tableName = $table->$mysqlTableColumn;
            
            // Saltar tablas del sistema
            if (in_array($tableName, ['migrations', 'failed_jobs', 'password_resets', 'personal_access_tokens'])) {
                continue;
            }

            $this->info("Migrando tabla: {$tableName}");

            try {
                // Verificar si la tabla existe en PostgreSQL
                if (!Schema::connection('pgsql')->hasTable($tableName)) {
                    $this->warn("Tabla {$tableName} no existe en PostgreSQL. Saltando...");
                    continue;
                }

                // Limpiar tabla en PostgreSQL
                DB::connection('pgsql')->table($tableName)->truncate();

                // Obtener datos de MySQL
                $data = DB::connection('mysql')->table($tableName)->get();

                if ($data->count() > 0) {
                    // Insertar datos en PostgreSQL en lotes
                    $chunks = $data->chunk(100);
                    foreach ($chunks as $chunk) {
                        DB::connection('pgsql')->table($tableName)->insert($chunk->toArray());
                    }
                    $this->info("✅ {$tableName}: {$data->count()} registros migrados");
                } else {
                    $this->info("⚠️  {$tableName}: tabla vacía");
                }

            } catch (\Exception $e) {
                $this->error("❌ Error migrando {$tableName}: " . $e->getMessage());
            }
        }

        $this->info('🎉 Migración completada!');
    }
}
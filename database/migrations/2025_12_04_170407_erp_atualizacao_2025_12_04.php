<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $path = database_path( "/../new_tables_api.sql");
        $sql = File::get($path);

        $create_tables = explode("\n\n", $sql);
        $create_tables = array_map( function($e){
            $e = str_replace( "CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $e );
            return $e;
        }, $create_tables );
        $sql_fixed = implode("\n\n", $create_tables);

        DB::unprepared( $sql_fixed );

        $path = database_path( "/../comand_api.sql");
        $sql = File::get($path);

        $alter_tables = explode("\n", $sql);

        foreach($alter_tables as $sql) {
            // @NOTE(patric):
            // Alternativa para rodar ALTER TABLE e não travar a execução
            // no caso da coluna já existir
            try {
                DB::unprepared($sql);
            } catch(\Throwable $e) {
                echo "SQL FAILED :: " . $e->getMessage() . "\n$sql\n\n";
            }
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

<?php
    namespace App\Commands\Database;

    use App\Console\Command;

    use function App\getDBConnection;

    use const App\SRC;

    class DumpTable extends Command {
        protected static string $fileDir = SRC . "/database/";
        protected string $table;

        protected static array $optionMap = [
            "table" => "table"
        ];
        
        public function run () {
            $sql = sprintf("DROP TABLE IF EXISTS %s;", $this->table);

            return getDBConnection()->exec($sql);
        }
    }
?>
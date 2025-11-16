<?php
    namespace App\Commands\Database;

    use App\Console\Command;

    use function App\getDBConnection;

    use const App\SRC;

    class CreateTable extends Command {
        protected static string $fileDir = SRC . "/database/";
        protected string $table;
        protected bool $force = false;

        protected static array $flagMap = [
            "f" => "force"
        ];

        protected static array $optionMap = [
            "table" => "table"
        ];
        
        public function run () {
            $sql = file_get_contents(sprintf("%s%s.sql", static::$fileDir, $this->table));

            if ($this->force) {
                DumpTable::from(["--table={$this->table}"])->run();
            }

            return getDBConnection()->exec($sql);
        }
    }
?>
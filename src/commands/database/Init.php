<?php
    namespace App\Commands\Database;

    use App\Console\Command;

    use function App\getDBConnection;

    use const App\SRC;

    class Init extends Command {
        protected static string $fileDir = SRC . "/database/";
        protected static string $fileName = "_.sql";
        protected static array $tables = ["User", "Request"];

        protected bool $force = false;

        protected static array $flagMap = [
            "f" => "force"
        ];
        
        public function run () {
            $sql = file_get_contents(static::$fileDir . DIRECTORY_SEPARATOR . static::$fileName);

            $result = getDBConnection()->exec($sql);

            if ($result === false) {
                echo "Failed to initialise the database.\n";
                return false;
            }

            if ($this->force) {
                foreach (array_reverse(static::$tables) as $table) {
                    $result = DumpTable::from(["--table={$table}"])->run();
    
                    if ($result === false) {
                        echo "Failed to delete the table: {$table}\n";
                        return false;
                    }
                }
            }

            foreach (static::$tables as $table) {
                $result = CreateTable::from(["--table={$table}"])->run();

                if ($result === false) {
                    echo "Failed to create the table: {$table}\n";
                    return false;
                }
            }

            return true;
        }
    }
?>
<?php
    namespace App\Commands\Database;

    use App\Console\Command;

    use function App\getDBConnection;

    use const App\SRC;

    class Init extends Command {
        protected static string $fileDir = SRC . "/database/";
        protected static string $fileName = "_.sql";

        protected bool $force = false;

        protected static array $flagMap = [
            "f" => "force"
        ];

        /**
         * Gets all SQL files in a directory, excluding the database schema.
         * @return array The paths to the table files.
         */
        protected static function getTableFiles (): array {
            $files = [];
            
            if (!is_dir(static::$fileDir)) return $files;
            
            foreach (scandir(static::$fileDir) as $file) {
                if ($file === '.' || $file === '..') continue;
                
                if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'sql') continue;

                if (pathinfo($file, PATHINFO_BASENAME) == static::$fileName) continue;
                
                $files[] = rtrim(static::$fileDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
            }
            
            return $files;
        }

        
        public function run () {
            $sql = file_get_contents(static::$fileDir . DIRECTORY_SEPARATOR . static::$fileName);

            $result = getDBConnection()->exec($sql);

            if ($result === false) {
                echo "Failed to initialise the database.\n";
                return false;
            }

            foreach (static::getTableFiles() as $file) {
                $tableName = pathinfo($file, PATHINFO_FILENAME);

                $args = ["--table={$tableName}"];

                if ($this->force) $args[] = "-f";

                $result = CreateTable::from($args)->run();

                if (!$result) {
                    echo "Failed to create the table: {$tableName}\n";
                    return false;
                }
            }

            return true;
        }
    }
?>
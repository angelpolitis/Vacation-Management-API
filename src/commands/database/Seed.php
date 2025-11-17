<?php
    namespace App\Commands\Database;

    use App\Console\Command;

    use function App\getDBConnection;

    use const App\SRC;

    class Seed extends Command {
        protected static string $fileDir = SRC . "/database/seed/";
        protected static array $seedFiles = ["User", "Request"];
        
        public function run () {
            foreach (array_reverse(static::$seedFiles) as $file) {
                $deleteResult = getDBConnection()->exec("DELETE FROM `$file`;");

                if ($deleteResult === false) {
                    echo "Failed to reset table '$file'.\n";
                    return false;
                }
            }

            foreach (static::$seedFiles as $file) {
                $content = file_get_contents(static::$fileDir . "$file.sql");

                $seedResult = getDBConnection()->exec($content);
                
                if ($seedResult === false) {
                    echo "Failed to reset table '$file'.\n";
                    return false;
                }
            }

            return true;
        }
    }
?>
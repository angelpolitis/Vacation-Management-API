<?php
    namespace App\Commands\Composer;

    use App\Console\Command;
    use RuntimeException;

    use const App\ROOT;

    class Install extends Command {
        protected string $version = "latest";
        protected bool $force = false;

        protected static array $flagMap = [
            "f" => "force"
        ];

        protected static array $optionMap = [
            "version" => "version"
        ];
        
        public function run () {
            $version = $this->version;

            if (getenv("IS_DOCKER") == 1) {
                throw new RuntimeException("Composer installation inside Docker containers is handled automatically.");
            }
        
            echo "Installing Composer (version: $version)...\n";
        
            # Determine the composer download URL using the version.
            $url = $version === "latest"
                ? "https://getcomposer.org/composer-stable.phar"
                : "https://getcomposer.org/download/$version/composer.phar";
        
            $composerPath = ROOT . "/composer.phar";

            if (file_exists($composerPath) && !$this->force) {
                throw new RuntimeException("composer.phar already exists. Use --force to overwrite.");
            }
        
            # Download composer.phar.
            echo "Downloading composer from: $url\n";
            $composerBinary = @file_get_contents($url);
        
            if ($composerBinary === false) {
                throw new RuntimeException("Failed to download Composer from $url");
            }
        
            file_put_contents($composerPath, $composerBinary);
        
            echo "composer.phar downloaded successfully.\n";
        
            # Create a composer shell file.
            $shPath = ROOT . '/composer.sh';
            $shContent = <<<SH
            #!/bin/bash
            php "\$(dirname "\$0")/composer.phar" "\$@"
            SH;
            file_put_contents($shPath, $shContent);
            chmod($shPath, 0755);
        
            # Create a composer bat file.
            $batPath = ROOT . '/composer.bat';
            $batContent = <<<BAT
            @echo off
            php "%~dp0composer.phar" %*
            BAT;
            file_put_contents($batPath, $batContent);
        
            echo "Wrapper scripts created: composer.sh, composer.bat\n";
        
            echo "Composer installation complete!\n";

            return true;
        }
    }
?>
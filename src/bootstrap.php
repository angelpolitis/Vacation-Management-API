<?php
    namespace App;

    # Register a autoloader that includes modules whose namespace follows folder structure.
    spl_autoload_register(function ($class) {
        $classParts = preg_split("#\\\\|\/#", $class);

        if ($classParts[0] != __NAMESPACE__) return;

        # Assume the class is a module.
        $path = __DIR__ . "/modules/" . implode(DIRECTORY_SEPARATOR, array_slice($classParts, 1)) . ".php";

        if (is_file($path)) {
            require_once $path;
            return;
        }

        # Assume the class is a command.
        $path = __DIR__ . "/commands/" . end($classParts);

        if (is_file($path)) require_once $path;
    });
?>
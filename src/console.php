<?php
    namespace App;

    require_once __DIR__ . "/bootstrap.php";

    use App\Console\Console;
    use Exception;

    # Terminate the script if the file isn't accessed via the command line.
    if (PHP_SAPI != "cli") {
        echo "This entry point can only be accessed via the command line.";
        return;
    }

    # Terminate the script if there are too few arguments.
    if ($argc <= 1) {
        echo "This entry point requires at least two arguments.";
        return;
    }

    $console = new Console(["verbose" => true]);

    $args = array_slice($argv, 2);

    $commandsRegistryPath = file_get_contents(__DIR__ . "/registry/commands.json");
    $commandsRegistry = json_decode($commandsRegistryPath, true);
    $command = $commandsRegistry[$argv[1]] ?? null;

    if (!isset($command)) {
        $console->warn("Unknown command.", true);
        return;
    }

    $path = __DIR__ . '/' . $command["file"];

    if (!is_file($path)) {
        $console->warn("Can't locate the implementation of the command.", true);
        return;
    }
    require_once $path;

    $class = str_replace('.', "\\", $command["class"]);

    if (!class_exists($class, false)) {
        $console->warn("Can't use the implementation of the command.", true);
        return;
    }

    try {
        $result = $console->runCommand(new $class($args), implode(" ", array_slice($argv, 1)));

        if (is_scalar($result)) echo $result . "\n";
        else echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
    }
    catch (Exception $e) {
        $console->warn($e->getMessage());
    }
?>
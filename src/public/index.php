<?php
    namespace App;

    require_once __DIR__ . "/../bootstrap.php";

    $composerPath = ROOT . "/vendor/autoload.php";

    if (file_exists($composerPath)) require_once $composerPath;

    require_once SRC . "/router.php";
?>
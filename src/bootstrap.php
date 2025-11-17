<?php
    namespace App;

    use App\Models\User;
    use PDO;
    use PDOException;

    const ROOT = __DIR__ . DIRECTORY_SEPARATOR . "..";
    const SRC = __DIR__;

    const DB_PORT = 3306; 
    const DB_USER = "angel";
    const DB_PASS = "12345678";
    const DB_NAME = "vacation_management_api";

    define("App\\DB_HOST", getenv("IS_DOCKER") == 1 ? "db" : "127.0.0.1");

    @session_start();

    define("USER_ID", $_SESSION["user_id"] ?? null);

    # Hide deprecation warnings that may occur due to outdated third-party libraries.
    error_reporting(E_ALL & ~E_DEPRECATED);

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
        $path = __DIR__ . "/commands/" . implode(DIRECTORY_SEPARATOR, array_slice($classParts, 2)) . ".php";

        if (is_file($path)) {
            require_once $path;
            return;
        }

        # Assume the class is a model.
        $path = __DIR__ . "/models/" . implode(DIRECTORY_SEPARATOR, array_slice($classParts, 2)) . ".php";

        if (is_file($path)) require_once $path;
    });

    define("IS_MANAGER", ((User::select(["id" => USER_ID], ["type"])[0] ?? [])["type"] ?? null) === "manager" ?? false);

    /**
     * Attempts to establish a database connection via PDO.
     * @throws PDOException If the operation fails.
     */
    function getDBConnection () : PDO {
        static $pdo = null;

        if ($pdo === null) {
            try {
                $pdo = new PDO(
                    "mysql:host=" . DB_HOST .";dbname=" . DB_NAME . ";charset=utf8mb4;port=" . DB_PORT,
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            }
            catch (PDOException $e) {
                die("Could not connect to the database: " . $e->getMessage());
            }
        }

        return $pdo;
    }

    function jsonInput (): array {
        $raw = file_get_contents("php://input");
        return json_decode($raw, true) ?? $_POST ?? [];
    }
    
    function jsonResponse ($data, int $code = 200): string {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        return '';
    }
    
    function guardAgainstGuest () : void {
        if (USER_ID === null) {
            echo jsonResponse(["status" => false, "error" => "Unauthorised"], 401);
            exit;
        }
    }

    function guardAgainstEmployee () : void {
        guardAgainstGuest();

        $userType = User::select(["id" => USER_ID], ["type"])[0] ?? null;

        if ($userType === null) {
            $_SESSION["user_id"] = null;
            guardAgainstGuest();
        }

        if ($userType["type"] !== "manager") {
            echo jsonResponse(["status" => false, "error" => "Forbidden"], 403);
            exit;
        }
    }
?>
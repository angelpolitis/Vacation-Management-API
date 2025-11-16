<?php
    namespace App;

    use InvalidArgumentException;
    use PDO;
    use ReflectionClass;

    use function App\getDBConnection;

    class Model {
        protected static string $table;
        protected array $data;
        protected array $primaryKey = ["id"];
        protected array $autoIncrement = ["id"];
        protected array $passwordFields = ["password"];
        protected array $requiredFields = [];

        public function __construct (array $data = []) {
            $this->data = $data;
        }

        private static function getTable () : string {
            return isset(static::$table) ? static::$table : (new ReflectionClass(static::class))->getShortName();
        }

        public function create () : bool {
            $pdo = getDBConnection();
    
            if (empty($this->data)) {
                throw new InvalidArgumentException("No data provided for insert.");
            }

            $data = $this->data;

            foreach ($data as $key => $value) {
                if (empty($value) && in_array($key, $this->requiredFields)) {
                    throw new InvalidArgumentException("Field '$key' is required and cannot be empty.");
                }

                if (in_array($key, $this->primaryKey) && !in_array($key, $this->autoIncrement)) {
                    throw new InvalidArgumentException("Primary key '$key' must be auto-increment or provided.");
                }

                if (in_array($key, $this->passwordFields)) {
                    $data[$key] = password_hash($value, PASSWORD_BCRYPT);
                }
            }

            $table = self::getTable();
    
            $columns = array_keys($data);
            $placeholders = array_map(fn($col) => ":$col", $columns);
    
            $sql = "INSERT INTO `$table` (" . implode(', ', $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
    
            $stmt = $pdo->prepare($sql);
    
            foreach ($data as $col => $value) {
                $stmt->bindValue(":$col", $value);
            }
    
            return $stmt->execute();
        }
    

        public function delete () : bool {
            $pdo = getDBConnection();

            # Prevent mass deletion.
            if (empty($this->data)) {
                throw new InvalidArgumentException('No conditions provided for delete.');
            }

            $table = self::getTable();

            # Build the WHERE clause of the query dynamically from the given data.
            $columns = array_keys($this->data);
            $conditions = array_map(fn ($col) => "`$col` = :$col", $columns);
            $sql = "DELETE FROM `$table` WHERE " . implode(' AND ', $conditions);

            $stmt = $pdo->prepare($sql);

            foreach ($this->data as $col => $value) $stmt->bindValue(":$col", $value);

            return $stmt->execute();
        }

        public static function from (array $data) : static {
            return new static($data);
        }

        public static function select (array $filter = [], array $fields = []) : array {
            $pdo = getDBConnection();
            $table = self::getTable();

            # Build the SELECT fields.
            $select = empty($fields) ? '*' : implode(', ', array_map(fn($f) => "`$f`", $fields));

            $sql = "SELECT $select FROM `$table`";

            # Build the WHERE clause.
            $params = [];
            if (!empty($filter)) {
                $conditions = [];
                foreach ($filter as $col => $value) {
                    $conditions[] = "`$col` = :$col";
                    $params[":$col"] = $value;
                }
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }


        public function update (array $data) : bool {
            $pdo = getDBConnection();

            $where = $this->data;

            if (empty($data)) {
                throw new InvalidArgumentException("No data provided for update.");
            }

            if (empty($where)) {
                throw new InvalidArgumentException("No conditions provided for update (WHERE clause).");
            }

            foreach ($data as $key => $value) {
                if (empty($value) && in_array($key, $this->requiredFields)) {
                    throw new InvalidArgumentException("Field '$key' is required and cannot be empty.");
                }

                if (in_array($key, $this->passwordFields)) {
                    $data[$key] = password_hash($value, PASSWORD_BCRYPT);
                }
            }

            $table = self::getTable();

            # Build the SET clause.
            $setParts = array_map(fn($col) => "`$col` = :$col", array_keys($data));

            # Build the WHERE clause.
            $whereParts = array_map(fn($col) => "`$col` = :where_$col", array_keys($where));

            $sql = "UPDATE `$table` SET " . implode(', ', $setParts) . " WHERE " . implode(' AND ', $whereParts);

            $stmt = $pdo->prepare($sql);

            foreach ($data as $col => $value) {
                $stmt->bindValue(":$col", $value);
            }

            foreach ($where as $col => $value) {
                $stmt->bindValue(":where_$col", $value);
            }

            return $stmt->execute();
        }

    }
?>
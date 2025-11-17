<?php
    namespace App\Models;

    use App\Model;
    use PDO;
    use RuntimeException;

    use function App\getDBConnection;

    class Request extends Model {
        protected array $editableFields = [
            "start_date",
            "end_date",
            "reason",
            "status",
            "decided_by"
        ];

        protected array $requiredFields = [
            "start_date",
            "end_date",
            "reason"
        ];

        public function approve (int $managerId) {
            $users = User::select(["id" => $managerId]);

            if (count($users) === 0) {
                throw new RuntimeException("The manager was not found", 404);
            }

            $user = $users[0];

            if ($user["role"] !== "manager") {
                throw new RuntimeException("The user is not a manager", 403);
            }

            return $this->update([
                "status" => "approved",
                "decided_by" => $managerId
            ]);
        }

        public function deleteByAuthor (int $authorId) {
            $requests = static::select(["id" => $this->data["id"]], ["requested_by", "status"]);

            if (count($requests) === 0) {
                throw new RuntimeException("Request not found", 404);
            }

            $request = $requests[0];

            if ($request["requested_by"] !== $authorId) {
                throw new RuntimeException("The user is not the author of the request", 403);
            }

            if ($request["status"] !== "pending") {
                throw new RuntimeException("Only pending requests can be deleted by the author", 403);
            }

            return $this->delete();
        }

        public function reject (int $managerId) {
            $users = User::select(["id" => $managerId]);

            if (count($users) === 0) {
                throw new RuntimeException("The manager was not found", 404);
            }

            $user = $users[0];

            if ($user["role"] !== "manager") {
                throw new RuntimeException("The user is not a manager", 403);
            }

            return $this->update([
                "status" => "rejected",
                "decided_by" => $managerId
            ]);
        }

        public static function selectWithUsers (array $filter = [], array $fields = []) : array {
            $pdo = getDBConnection();
        
            $select = empty($fields) ? '`r`.*' : implode(', ', array_map(fn($f) => "`r`.`$f`", $fields));
            
            $sql = <<<SQL
                SELECT 
                    $select,
                    `u_req`.`id` AS `requested_by_id`,
                    `u_req`.`name` AS `requested_by_name`,
                    `u_req`.`email` AS `requested_by_email`,
                    `u_req`.`type` AS `requested_by_type`,
                    `u_dec`.`id` AS `decided_by_id`,
                    `u_dec`.`name` AS `decided_by_name`,
                    `u_dec`.`email` AS `decided_by_email`,
                    `u_dec`.`type` AS `decided_by_type`
                FROM `Request` `r`
                LEFT JOIN `User` `u_req` ON `r`.`requested_by` = `u_req`.`id`
                LEFT JOIN `User` `u_dec` ON `r`.`decided_by` = `u_dec`.`id`
            SQL;
        
            # Assemble the WHERE clause.
            $params = [];
            if (!empty($filter)) {
                $conditions = [];
                foreach ($filter as $col => $value) {
                    $conditions[] = "r.`$col` = :$col";
                    $params[":$col"] = $value;
                }
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
        
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            # Transform the result into the desired structure.
            return array_map(function($row) {
                return [
                    "id" => $row["id"],
                    "start_date" => $row["start_date"],
                    "end_date" => $row["end_date"],
                    "reason" => $row["reason"],
                    "submission_date" => $row["submission_date"],
                    "status" => $row["status"],
                    "requested_by" => $row["requested_by_id"] ? [
                        "id" => $row["requested_by_id"],
                        "name" => $row["requested_by_name"],
                        "email" => $row["requested_by_email"],
                        "type" => $row["requested_by_type"]
                    ] : null,
                    "decided_by" => $row["decided_by_id"] ? [
                        "id" => $row["decided_by_id"],
                        "name" => $row["decided_by_name"],
                        "email" => $row["decided_by_email"],
                        "type" => $row["decided_by_type"]
                    ] : null
                ];
            }, $rows);
        }
        
        public static function selectSettled (array $fields = []) : array {
            $pdo = getDBConnection();
        
            $select = empty($fields) ? '`r`.*' : implode(', ', array_map(fn($f) => "`r`.`$f`", $fields));
            
            $sql = <<<SQL
                SELECT 
                    $select,
                    `u_req`.`id` AS `requested_by_id`,
                    `u_req`.`name` AS `requested_by_name`,
                    `u_req`.`email` AS `requested_by_email`,
                    `u_req`.`type` AS `requested_by_type`,
                    `u_dec`.`id` AS `decided_by_id`,
                    `u_dec`.`name` AS `decided_by_name`,
                    `u_dec`.`email` AS `decided_by_email`,
                    `u_dec`.`type` AS `decided_by_type`
                FROM `Request` `r`
                LEFT JOIN `User` `u_req` ON `r`.`requested_by` = `u_req`.`id`
                LEFT JOIN `User` `u_dec` ON `r`.`decided_by` = `u_dec`.`id`
                WHERE `r`.`status` IN ('approved', 'rejected')
            SQL;
        
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            # Transform the result into the desired structure.
            return array_map(function($row) {
                return [
                    "id" => $row["id"],
                    "start_date" => $row["start_date"],
                    "end_date" => $row["end_date"],
                    "reason" => $row["reason"],
                    "submission_date" => $row["submission_date"],
                    "status" => $row["status"],
                    "requested_by" => $row["requested_by_id"] ? [
                        "id" => $row["requested_by_id"],
                        "name" => $row["requested_by_name"],
                        "email" => $row["requested_by_email"],
                        "type" => $row["requested_by_type"]
                    ] : null,
                    "decided_by" => $row["decided_by_id"] ? [
                        "id" => $row["decided_by_id"],
                        "name" => $row["decided_by_name"],
                        "email" => $row["decided_by_email"],
                        "type" => $row["decided_by_type"]
                    ] : null
                ];
            }, $rows);
        }
    }
?>
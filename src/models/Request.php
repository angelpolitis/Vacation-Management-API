<?php
    namespace App\Models;

    use App\Model;
    use RuntimeException;

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
    }
?>
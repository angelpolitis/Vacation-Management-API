<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\User;

    class DeleteUser extends Command {
        protected ?string $id = null;
        protected ?string $employeeCode = null;

        protected static array $flagMap = [];

        protected static array $optionMap = [
            "id" => "id",
            "employee_code" => "employeeCode"
        ];
        
        public function run () {
            if ($this->id === null && $this->employeeCode === null) {
                echo "Either 'id' or 'employee_code' must be provided to delete a user.\n";
                return 1;
            }

            $args = [];

            if ($this->id !== null) {
                $args["id"] = $this->id;
            }

            if ($this->employeeCode !== null) {
                $args["employee_code"] = $this->employeeCode;
            }

            return User::from($args)->delete();
        }
    }
?>
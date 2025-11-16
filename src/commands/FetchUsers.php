<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\User;

    class FetchUsers extends Command {
        protected ?string $id = null;
        protected ?string $name = null;
        protected ?string $email = null;
        protected ?string $password = null;
        protected ?string $employeeCode = null;
        protected ?string $type = null;

        protected static array $optionMap = [
            "id" => "id",
            "name" => "name",
            "email" => "email",
            "password" => "password",
            "employee_code" => "employeeCode",
            "type" => "type"
        ];
        
        public function run () {
            $args = [];

            foreach ($this->getOptions() as $field => $value) {
                $args[$field] = $this->{static::$optionMap[$field]};
            }

            return User::select($args, ["id", "name", "email", "employee_code", "type"]);
        }
    }
?>
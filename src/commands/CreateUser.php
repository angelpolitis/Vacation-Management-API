<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\User;

    class CreateUser extends Command {
        protected ?string $id = null;
        protected string $name;
        protected string $email;
        protected string $password;
        protected string $employeeCode;
        protected string $type = "employee";

        protected static array $optionMap = [
            "name" => "name",
            "email" => "email",
            "password" => "password",
            "employee_code" => "employeeCode",
            "type" => "type"
        ];
        
        public function run () {
            $args = array_map(fn ($field) => $this->{$field} ?? null, static::$optionMap);

            return User::from($args)->create();
        }
    }
?>
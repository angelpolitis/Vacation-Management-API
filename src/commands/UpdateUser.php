<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\User;

    class UpdateUser extends Command {
        protected ?string $id;
        protected ?string $name;
        protected ?string $email;
        protected ?string $password;
        protected ?string $employeeCode;
        protected ?string $type;
        protected ?string $filter;

        protected static array $optionMap = [
            "id" => "id",
            "name" => "name",
            "email" => "email",
            "password" => "password",
            "employee_code" => "employeeCode",
            "type" => "type",
            "filter" => "filter"
        ];
        
        public function run () {
            parse_str($this->filter ?? "", $output);

            $filter = [];

            foreach ($output as $key => $value) {
                if (!isset(static::$optionMap[$key])) continue;

                $filter[$key] = $value;
            }

            $args = [];

            foreach ($this->getOptions() as $field => $value) {
                if ($field == "filter") continue;

                $args[$field] = $this->{static::$optionMap[$field]};
            }

            return User::from($filter)->update($args);
        }
    }
?>
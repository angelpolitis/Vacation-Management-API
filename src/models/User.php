<?php
    namespace App\Models;

    use App\Model;

    class User extends Model {
        protected array $requiredFields = [
            "name",
            "email",
            "password",
            "employee_code"
        ];
    }
?>
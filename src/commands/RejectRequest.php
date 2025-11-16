<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\Request;

    class RejectRequest extends Command {
        protected int $id;
        protected int $user;

        protected static array $optionMap = [
            "id" => "id",
            "user" => "user"
        ];
        
        public function run () {
            return Request::from(["id" => $this->id])->reject($this->user);
        }
    }
?>
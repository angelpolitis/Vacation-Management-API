<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\Request;

    class ApproveRequest extends Command {
        protected int $id;
        protected int $user;

        protected static array $optionMap = [
            "id" => "id",
            "user" => "user"
        ];
        
        public function run () {
            return Request::from(["id" => $this->id])->approve($this->user);
        }
    }
?>
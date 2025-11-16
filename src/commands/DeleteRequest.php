<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\Request;

    class DeleteRequest extends Command {
        protected int $id;

        protected static array $optionMap = [
            "id" => "id"
        ];
        
        public function run () {
            return Request::from(["id" => $this->id])->delete();
        }
    }
?>
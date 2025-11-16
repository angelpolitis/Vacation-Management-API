<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\Request;

    class CreateRequest extends Command {
        protected string $startDate;
        protected string $endDate;
        protected string $reason;
        protected int $user;

        protected static array $optionMap = [
            "start_date" => "startDate",
            "end_date" => "endDate",
            "reason" => "reason",
            "user" => "user"
        ];
        
        public function run () {
            return Request::from([
                "start_date" => $this->startDate,
                "end_date" => $this->endDate,
                "reason" => $this->reason,
                "requested_by" => $this->user
            ])->create();
        }
    }
?>
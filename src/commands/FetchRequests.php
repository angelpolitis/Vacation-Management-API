<?php
    namespace App\Commands;

    use App\Console\Command;
    use App\Models\Request;

    class FetchRequests extends Command {
        protected ?int $id = null;
        protected ?string $startDate = null;
        protected ?string $endDate = null;
        protected ?string $submissionDate = null;
        protected ?string $status = null;
        protected ?int $requestedBy = null;
        protected ?int $decidedBy = null;

        protected static array $optionMap = [
            "id" => "id",
            "start_date" => "startDate",
            "end_date" => "endDate",
            "submission_date" => "submissionDate",
            "status" => "status",
            "requested_by" => "requestedBy",
            "decided_by" => "decidedBy"
        ];
        
        public function run () {
            $args = [];

            foreach ($this->getOptions() as $field => $value) {
                $args[$field] = $this->{static::$optionMap[$field]};
            }

            return Request::select($args);
        }
    }
?>
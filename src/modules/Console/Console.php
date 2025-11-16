<?php
    namespace App\Console;

    class Console {
        /**
         * Whether warnings are printed during a command's execution.
         * @var bool
         */
        protected bool $verbose = false;

        /**
         * Creates a new console.
         * @param array $options The options to initialise the console with.
         */
        public function __construct (array $options = []) {
            if (isset($options["verbose"])) $this->verbose = boolval($options["verbose"]);
        }

        /**
         * Try to decode a value as JSON, if possible.
         * @param string $value A value.
         * @return mixed The decoded array if the value is JSON or the value itself otherwise.
         */
        protected static function maybeParseJson (string $value) : mixed {
            $data = json_decode($value, true);
            
            return json_last_error() === JSON_ERROR_NONE ? $data : $value;
        }

        /**
         * Parses a command-line expression into a number of commands.
         * @param string $expression A raw command-line expression.
         * @return array The parsed arguments as an array.
         */
        public static function parse (string $expression) : array {
            $commands = [];
            $currentCommand = '';
            $inQuotes = false;
            $quoteChar = '';

            for ($i = 0, $len = strlen($expression); $i < $len; $i++) {
                $char = $expression[$i];

                if ($inQuotes) {
                    if ($char === $quoteChar) {
                        $inQuotes = false;
                        $quoteChar = '';
                    }
                    elseif ($char === '\\' && $i + 1 < $len && $expression[$i + 1] === $quoteChar) {
                        $currentCommand .= $expression[++$i];
                    }
                    else $currentCommand .= $char;
                }
                else {
                    if ($char === '"' || $char === "'") {
                        $inQuotes = true;
                        $quoteChar = $char;
                    }
                    elseif (substr($expression, $i, 2) === '&&' || substr($expression, $i, 2) === '||') {
                        if ($currentCommand !== '') {
                            $commands[] = trim($currentCommand);
                        }
                        $commands[] = substr($expression, $i, 2);
                        $currentCommand = '';
                        $i++;
                    }
                    elseif ($char === ' ') {
                        if ($currentCommand !== '') {
                            $currentCommand .= $char;
                        }
                    }
                    else $currentCommand .= $char;
                }
            }

            if ($currentCommand !== '') {
                $commands[] = trim($currentCommand);
            }

            return $commands;
        }

        /**
         * Parses a command into an array of arguments.
         * @param string $command A raw command-line string.
         * @return array The parsed arguments as an array.
         */
        public static function parseCommand (string $command) : array {
            $args = [];
            $currentArg = '';
            $inQuotes = false;
            $quoteChar = '';

            for ($i = 0, $len = strlen($command); $i < $len; $i++) {
                $char = $command[$i];

                if ($inQuotes) {
                    if ($char === $quoteChar) {
                        $inQuotes = false;
                        $quoteChar = '';
                    }
                    elseif ($char === '\\' && $i + 1 < $len && $command[$i + 1] === $quoteChar) {
                        $currentArg .= $command[++$i];
                    }
                    else $currentArg .= $char;
                }
                else {
                    if ($char === '"' || $char === "'") {
                        $inQuotes = true;
                        $quoteChar = $char;
                    }
                    elseif ($char === ' ') {
                        if ($currentArg !== '') {
                            $args[] = $currentArg;
                            $currentArg = '';
                        }
                    }
                    else $currentArg .= $char;
                }
            }

            if ($currentArg !== '') {
                $args[] = $currentArg;
            }

            return $args;
        }

        /**
         * Parses a series of command arguments.
         * @param array $args The arguments to parse.
         * @return array An array of composite parts for a command.
         */
        public static function parseCommandArgs (array $args) : array {
            $result = [
                "options" => [],
                "flags" => [],
                "args" => []
            ];

            foreach ($args as $arg) {
                if (strpos($arg, "--") === 0) {
                    $parts = explode('=', substr($arg, 2), 2);
                    $key = $parts[0];
                    $value = $parts[1] ?? true;
                    $result["options"][$key] = static::maybeParseJson($value);
                }
                elseif (strpos($arg, '-') === 0) {
                    $result["flags"] = array_merge($result["flags"], str_split(substr($arg, 1)));
                }
                else {
                    $result["args"][] = static::maybeParseJson($arg);
                }
            }

            return $result;
        }

        /**
         * Runs a command or expression given as a string.
         * @param string $expression A command or expression.
         * @return array The return values of the commands.
         */
        public function run (string $expression) : array {
            $expressionParts = static::parse($expression);

            $success = true;

            $result = [];

            for ($i = 0; $i < count($expressionParts); $i++) {
                $part = $expressionParts[$i];

                if ($part === "&&") {
                    if (!$success) $i++;
                }
                elseif ($part === "||") {
                    if ($success) $i++;
                }
                else {
                    $result[] = $this->runCommand(new Command($part));
                }
            }

            return $result;
        }

        /**
         * Runs a command.
         * @param Command $command A command.
         * @return mixed The return value of the command.
         */
        public function runCommand (Command $command) : mixed {
            $unknownFlags = $command->getUnknownFlags();

            if (!empty($unknownFlags)) {
                $this->warn("There were unknown flags found: " . implode(", ", array_map(fn ($f) => "'$f'", $unknownFlags)));
            }

            $result = $command->run();

            return $result;
        }

        /**
         * Posts a warning.
         * @param string $message The message to post.
         * @param bool $force Whether to force-post the message even if verbosity is off.
         */
        public function warn (string $message, bool $force = false) : void {
            if ($this->verbose || $force) echo "[i] $message" . PHP_EOL;
        }
    }
?>
<?php
    namespace App\Console;

    use Exception;
    use ReflectionProperty;
    use Throwable;

    abstract class Command {
        /**
         * The map that binds the arguments of a command to its relevant properties.
         * @var string[]
         */
        protected static array $argMap = [];

        /**
         * The map that binds the flags of a command to its relevant properties.
         * @var string[]
         */
        protected static array $flagMap = [];

        /**
         * The map that binds the options of a command to its relevant properties.
         * @var string[]
         */
        protected static array $optionMap = [];

        /**
         * The positional arguments of a command.
         * @var mixed[]
         */
        protected array $args = [];

        /**
         * The flags of a command.
         * @var string[]
         */
        protected array $flags = [];

        /**
         * The options of a command.
         * @var mixed[]
         */
        protected array $options = [];

        /**
         * The unknown flags of a command.
         * @var string[]
         */
        protected array $unknownFlags = [];

        /**
         * The unknown options of a command.
         * @var string[]
         */
        protected array $unknownOptions = [];

        /**
         * Creates a new command.
         * @param array|string $command A command-line expression or an array of command-line arguments.
         */
        public function __construct (array | string $command) {
            $args = is_string($command) ? Console::parseCommand($command) : $command;

            [
                "options" => $this->options,
                "flags" => $this->flags,
                "args" => $this->args
            ] = Console::parseCommandArgs($args);
            
            try {
                foreach (static::$flagMap as $key => $property) {
                    $this->{$property} = $this->hasFlag($key);
                }
            }
            catch (Throwable $e) {
                throw new Exception("The flag '$property' must be defined as boolean.");
            }

            foreach ($this->getFlags() as $flag) {
                if (isset(static::$flagMap[$flag])) continue;

                $this->unknownFlags[] = $flag;
            }
            
            foreach (static::$optionMap as $option => $property) {
                try {
                    $this->{$property} = $this->getOption($option);
                }
                catch (Throwable $e) {
                    $property = new ReflectionProperty($this, $property);

                    if ($property->hasDefaultValue()) {
                        continue;
                    }
                    
                    $type = $property->getType();
                    
                    $value = json_encode($this->getOption($option));

                    throw new Exception("The option '$property' of type '$type' can't have the value '$value'.");
                }
            }

            foreach ($this->getOptions() as $option => $_) {
                if (isset(static::$optionMap[$option])) continue;

                $this->unknownOptions[] = $option;
            }

            $args = $this->getArgs(sizeof(static::$argMap));

            try {
                foreach (static::$argMap as $index => $property) {
                    $this->{$property} = $args[$index];
                }
            }
            catch (Throwable $e) {
                $type = (new ReflectionProperty($this, $property))->getType();

                $value = json_encode($args[$index]);

                throw new Exception("The required argument '$property' of type '$type' can't have the value '$value'.");
            }
        }
        
        /**
         * Parses an expression or array of arguments into a command.
         * @return static A new command.
         */
        public static function from (array | string $command) : static {
            return new static($command);
        }

        /**
         * Gets the arguments of a command.
         * @param int $length The desired minimum length of the arguments array; empty places will be filled with the default value.
         * @param mixed $default A default value for a parameter that has no value.
         * @return array The arguments of a command.
         */
        public function getArgs (int $length = 0, mixed $default = null) : array {
            $args = $this->args;

            for ($i = sizeof($args); $i < $length; $i++) $args[] = $default;

            return $args;
        }

        /**
         * Gets the flags of a command.
         * @return string[] The flags of a command.
         */
        public function getFlags () : array {
            return $this->flags;
        }

        /**
         * Gets the arguments of a command.
         * @return string[] The known flags of a command.
         */
        public static function getKnownArgs () : array {
            return array_keys(static::$argMap);
        }

        /**
         * Gets the flags of a command.
         * @return string[] The known flags of a command.
         */
        public static function getKnownFlags () : array {
            return array_keys(static::$flagMap);
        }

        /**
         * Gets the value of an option of a command.
         * @param string $option The name of the option.
         * @param mixed $default A default value for the option it it has no value.
         * @return mixed The value of the option.
         */
        public function getOption (string $option, mixed $default = null) : mixed {
            return $this->options[$option] ?? $default;
        }

        /**
         * Gets the options of a command.
         * @return string[] The options of a command.
         */
        public function getOptions () : array {
            return $this->options;
        }

        /**
         * Gets the unknown flags of a command.
         * @return string[] The unknown flags of a command.
         */
        public function getUnknownFlags () : array {
            return $this->unknownFlags;
        }

        /**
         * Gets the unknown options of a command.
         * @return string[] The unknown options of a command.
         */
        public function getUnknownOptions () : array {
            return $this->unknownOptions;
        }

        /**
         * Gets whether a command has a specific flag.
         * @param string $flag The name of the flag.
         * @return bool Whether the flag has been specified.
         */
        public function hasFlag (string $flag) : bool {
            return in_array($flag, $this->flags);
        }

        /**
         * Gets whether a command has a specific option.
         * @param string $option The name of the option.
         * @return bool Whether the option has been specified.
         */
        public function hasOption (string $option) : bool {
            return in_array($option, $this->options);
        }

        /**
         * Implements and executes a command in the context of a specification.
         */
        abstract public function run ();
    }
?>
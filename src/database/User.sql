CREATE TABLE IF NOT EXISTS `User` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR (50) NOT NULL,
    `email` VARCHAR (255) NOT NULL,
    `employee_code` CHAR (7) NOT NULL,
    `password` VARCHAR (60) NOT NULL,
    `type` ENUM ('employee', 'manager') NOT NULL DEFAULT 'employee',
    PRIMARY KEY (`id`),
    CONSTRAINT `unique_employee_code` UNIQUE (`employee_code`),
    CONSTRAINT `unique_email` UNIQUE (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
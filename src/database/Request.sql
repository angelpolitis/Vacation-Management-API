CREATE TABLE IF NOT EXISTS `Request` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `requested_by` INT UNSIGNED NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `reason` VARCHAR (255) NOT NULL,
    `submission_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM ('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `decided_by` INT UNSIGNED NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_request_requester` FOREIGN KEY (`requested_by`) REFERENCES `User`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_request_decider` FOREIGN KEY (`decided_by`) REFERENCES `User`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
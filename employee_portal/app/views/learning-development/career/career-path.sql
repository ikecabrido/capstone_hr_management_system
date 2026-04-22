CREATE TABLE career_paths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    target_position VARCHAR(100) NOT NULL,
    prerequisites TEXT,
    skills_required JSON,
    duration_months INT NOT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    cover_photo VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `idp_progress` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,

  `idp_id` INT NOT NULL,

  `item_type` ENUM('course','program') NOT NULL,
  `item_id` INT NOT NULL,

  `status` ENUM('pending','in-progress','completed') DEFAULT 'pending',

  `completed_at` DATETIME DEFAULT NULL,

  FOREIGN KEY (`idp_id`) REFERENCES `individual_development_plans`(`idp_id`)
    ON DELETE CASCADE
);

CREATE TABLE `individual_development_plans` (
  `idp_id` INT(11) NOT NULL AUTO_INCREMENT,

  `user_id` INT(11) NOT NULL,
  `career_path_id` INT(11) DEFAULT NULL,

  `start_date` DATE NOT NULL,
  `end_date` DATE DEFAULT NULL,

  `objectives` TEXT NOT NULL,
  `milestones` JSON DEFAULT NULL,

  `status` ENUM('active','completed','cancelled') DEFAULT 'active',

  `created_by` INT(11) DEFAULT NULL,

  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`idp_id`),

  -- optional foreign keys (recommended if your DB supports it cleanly)
  CONSTRAINT `fk_idp_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE,

  CONSTRAINT `fk_idp_career_path`
    FOREIGN KEY (`career_path_id`) REFERENCES `career_paths`(`id`)
    ON DELETE SET NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
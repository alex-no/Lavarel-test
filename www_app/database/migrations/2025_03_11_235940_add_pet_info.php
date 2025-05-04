<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `pet_types` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_uk` VARCHAR(255) NULL DEFAULT NULL,
  `name_en` VARCHAR(255) NULL DEFAULT NULL,
  `name_ru` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;

CREATE TABLE IF NOT EXISTS `pet_breeds` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pet_type_id` INT(10) UNSIGNED NOT NULL,
  `name_uk` VARCHAR(255) NULL DEFAULT NULL,
  `name_en` VARCHAR(255) NULL DEFAULT NULL,
  `name_ru` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_pet_type_id1_idx` (`pet_type_id` ASC),
  CONSTRAINT `fk_pet_type_id1`
    FOREIGN KEY (`pet_type_id`)
    REFERENCES `pet_types` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;

CREATE TABLE IF NOT EXISTS `pet_owners` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT(19) UNSIGNED NOT NULL,
  `pet_type_id` INT(10) UNSIGNED NOT NULL,
  `pet_breed_id` INT(10) UNSIGNED NULL DEFAULT NULL,
  `nickname_uk` VARCHAR(255) NULL DEFAULT NULL,
  `nickname_en` VARCHAR(255) NULL DEFAULT NULL,
  `nickname_ru` VARCHAR(255) NULL DEFAULT NULL,
  `year_of_birth` MEDIUMINT(9) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_pet_type_id2_idx` (`pet_type_id` ASC),
  INDEX `fk_pet_breed_id1_idx` (`pet_breed_id` ASC),
  INDEX `fk_user_id1_idx` (`user_id` ASC),
  CONSTRAINT `fk_pet_breed_id1`
    FOREIGN KEY (`pet_breed_id`)
    REFERENCES `pet_breeds` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_pet_type_id2`
    FOREIGN KEY (`pet_type_id`)
    REFERENCES `pet_types` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_user_id1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

SQL;

        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_owners');
        Schema::dropIfExists('pet_breeds');
        Schema::dropIfExists('pet_types');
    }
};

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `sites` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL DEFAULT NULL,
  `url` VARCHAR(255)  NOT NULL,
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `status` CHAR(1) NULL DEFAULT NULL,
  `secret` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

ALTER TABLE `videos` 
ADD COLUMN `sites_id` INT(11) NULL,
ADD INDEX `fk_videos_sites1_idx` (`sites_id` ASC);

ALTER TABLE `videos` 
ADD CONSTRAINT `fk_videos_sites1`
  FOREIGN KEY (`sites_id`)
  REFERENCES `sites` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

UPDATE configurations SET  version = '7.3', modified = now() WHERE id = 1;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

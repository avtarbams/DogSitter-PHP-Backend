
-- -----------------------------------------------------
-- Table `welovepets`.`product_details`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `welovepets`.`product_details` ;

CREATE TABLE IF NOT EXISTS `welovepets`.`product_details` (
  `product_details_id` INT NOT NULL AUTO_INCREMENT,
  `product_name` VARCHAR(100) NULL,
  `product_image_url` VARCHAR(100) NULL,
  `product_data` TEXT(500) NULL,
  `product_availability` INT NULL,
  `product_delete_flag` TINYINT(1) NULL,
  `product_amount` FLOAT NULL,
  PRIMARY KEY (`product_details_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `welovepets`.`product_purchase_details`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `welovepets`.`product_purchase_details` ;

CREATE TABLE IF NOT EXISTS `welovepets`.`product_purchase_details` (
  `ppd_id` INT NOT NULL AUTO_INCREMENT,
  `product_details_product_details_id` INT NOT NULL,
  `user_details_userid` INT NOT NULL,
  PRIMARY KEY (`ppd_id`),
  INDEX `fk_product_purchase_details_product_details1_idx` (`product_details_product_details_id` ASC),
  INDEX `fk_product_purchase_details_user_details1_idx` (`user_details_userid` ASC),
  CONSTRAINT `fk_product_purchase_details_product_details1`
    FOREIGN KEY (`product_details_product_details_id`)
    REFERENCES `welovepets`.`product_details` (`product_details_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_purchase_details_user_details1`
    FOREIGN KEY (`user_details_userid`)
    REFERENCES `welovepets`.`user_details` (`userid`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;
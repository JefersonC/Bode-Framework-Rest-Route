-- MySQL Script generated by MySQL Workbench
-- 08/16/16 21:52:14
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema ponto
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `ponto` ;

-- -----------------------------------------------------
-- Schema ponto
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ponto` DEFAULT CHARACTER SET utf8 ;
USE `ponto` ;

-- -----------------------------------------------------
-- Table `ponto`.`cargo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ponto`.`cargo` ;

CREATE TABLE IF NOT EXISTS `ponto`.`cargo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(20) NOT NULL,
  `cargaHoraria` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ponto`.`usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ponto`.`usuario` ;

CREATE TABLE IF NOT EXISTS `ponto`.`usuario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `senha` VARCHAR(45) NOT NULL,
  `nome` VARCHAR(45) NOT NULL,
  `idCargo` INT NOT NULL,
  `identificacao` VARCHAR(20) NULL,
  `rfid` VARCHAR(16) NULL,
  `dataCadastro` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `dataAdmissao` DATETIME NULL,
  `estado` INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `idxRfid` (`rfid`(16) ASC),
  INDEX `fkUsuarioCargo_idx` (`idCargo` ASC),
  CONSTRAINT `fkUsuarioCargo`
    FOREIGN KEY (`idCargo`)
    REFERENCES `ponto`.`cargo` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ponto`.`ponto`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ponto`.`ponto` ;

CREATE TABLE IF NOT EXISTS `ponto`.`ponto` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idUsuario` INT NOT NULL,
  `estado` INT(1) NOT NULL DEFAULT 1,
  `dataAbertura` DATETIME NOT NULL,
  `dataFechamento` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `fkPontoUsuario_idx` (`idUsuario` ASC),
  CONSTRAINT `fkPontoUsuario`
    FOREIGN KEY (`idUsuario`)
    REFERENCES `ponto`.`usuario` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ponto`.`sistema`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ponto`.`sistema` ;

CREATE TABLE IF NOT EXISTS `ponto`.`sistema` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `versao` DOUBLE(4,2) NOT NULL DEFAULT 0,
  `cargaHoraria` INT NOT NULL DEFAULT 160,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

USE `ponto` ;

-- -----------------------------------------------------
-- Placeholder table for view `ponto`.`pontoFechado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ponto`.`pontoFechado` (`id` INT);

-- -----------------------------------------------------
-- View `ponto`.`pontoFechado`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `ponto`.`pontoFechado` ;
DROP TABLE IF EXISTS `ponto`.`pontoFechado`;
USE `ponto`;
CREATE  OR REPLACE VIEW `pontoFechado` AS (
	SELECT id, dataAbertura, dataFechamento, SUM(TIMESTAMPDIFF(MINUTE, dataAbertura, dataFechamento)) * 60 as 'horas' FROM ponto WHERE estado = 2
);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `ponto`.`cargo`
-- -----------------------------------------------------
START TRANSACTION;
USE `ponto`;
INSERT INTO `ponto`.`cargo` (`id`, `descricao`, `cargaHoraria`) VALUES (1, 'Administrador', 80);

COMMIT;


-- -----------------------------------------------------
-- Data for table `ponto`.`usuario`
-- -----------------------------------------------------
START TRANSACTION;
USE `ponto`;
INSERT INTO `ponto`.`usuario` (`id`, `email`, `senha`, `nome`, `idCargo`, `identificacao`, `rfid`, `dataCadastro`, `dataAdmissao`, `estado`) VALUES (1, 'admin@admin', '21232f297a57a5a743894a0e4a801fc3', 'Administrador', 1, NULL, NULL, NULL, NULL, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `ponto`.`sistema`
-- -----------------------------------------------------
START TRANSACTION;
USE `ponto`;
INSERT INTO `ponto`.`sistema` (`id`, `versao`, `cargaHoraria`) VALUES (1, 0.1, 160);

COMMIT;


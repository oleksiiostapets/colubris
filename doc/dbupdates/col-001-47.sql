ALTER TABLE `log` ADD COLUMN `organisation_id` INT(11) NOT NULL;
UPDATE `log` SET `organisation_id` = (SELECT `id` FROM `organisation` WHERE `name` = 'AgileTech' LIMIT 0,1) WHERE organisation_id < 1;

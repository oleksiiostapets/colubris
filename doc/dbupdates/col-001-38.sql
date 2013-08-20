ALTER TABLE `quote` ADD COLUMN `organisation_id` INT(11) NOT NULL;
UPDATE `quote` SET `organisation_id` = (SELECT `id` FROM `organisation` WHERE `name` = 'AgileTech' LIMIT 0,1) WHERE organisation_id < 1;

ALTER TABLE `task` ADD COLUMN `organisation_id` INT(11) NOT NULL;
UPDATE `task` SET `organisation_id` = (SELECT `id` FROM `organisation` WHERE `name` = 'AgileTech' LIMIT 0,1) WHERE organisation_id < 1;

ALTER TABLE `client` ADD COLUMN `organisation_id` INT(11) NOT NULL;
UPDATE `client` SET `organisation_id` = (SELECT `id` FROM `organisation` WHERE `name` = 'AgileTech' LIMIT 0,1) WHERE organisation_id < 1;

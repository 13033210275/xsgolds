#20170910
ALTER TABLE `xs_adv` ADD COLUMN `adv_type` TINYINT(2) NOT NULL DEFAULT '0' AFTER `flag`;

CREATE TABLE `xs_recommend_merchant` (
	`id` INT(10) UNSIGNED NOT NULL,
	`status` TINYINT(1) UNSIGNED NOT NULL,
	`rise` INT(4) UNSIGNED NOT NULL DEFAULT '0'
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

ALTER TABLE `xs_recommend_merchant` ADD COLUMN `txt` VARCHAR(100) NOT NULL DEFAULT ""  AFTER `rise`;


CREATE TABLE IF NOT EXISTS `tokens_tags_ctags` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `ctag` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `tokens_tags_ctags` ADD INDEX ( `ctag` ) ;

ALTER TABLE  `tokens_tags_optimized` ADD `ctag_id` INT;
ALTER TABLE  `tokens_tags_optimized` ADD FOREIGN KEY (  `ctag_id` ) REFERENCES  `tokens_tags_ctags` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

INSERT INTO `tokens_tags_ctags`(`ctag`) SELECT DISTINCT ctag FROM `tokens_tags_optimized`;
UPDATE `tokens_tags_optimized` tto JOIN `tokens_tags_ctags` ttc USING(ctag) SET tto.ctag_id = ttc.id;

ALTER TABLE  `tokens_tags_optimized` ADD  `pos` VARCHAR( 20 ) NOT NULL ;
UPDATE `tokens_tags_optimized` SET pos = SUBSTRING_INDEX( SUBSTRING_INDEX(ctag,  ':', 1 ) ,  ':', -1 );
ALTER TABLE `tokens_tags_optimized`  DROP `ctag`;
ALTER TABLE `tokens_tags_optimized` CHANGE `ctag_id` `ctag_id` INT( 11 ) NOT NULL ;

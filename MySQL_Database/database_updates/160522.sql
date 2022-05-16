ALTER TABLE `system` ADD COLUMN IF NOT EXISTS `theme` TINYINT(4) NULL AFTER `page_refresh`;
DROP TABLE IF EXISTS `theme`;
CREATE TABLE IF NOT EXISTS `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sync` tinyint(4) NOT NULL,
  `purge` tinyint(4) NOT NULL COMMENT 'Mark For Deletion',
  `name` char(50) COLLATE utf8_bin,
  `row_justification` char(50) COLLATE utf8_bin,
  `background_color` char(50) COLLATE utf8_bin,
  `text_color` char(50) COLLATE utf8_bin,
  `border_color` char(50) COLLATE utf8_bin,
  `footer_color` char(50) COLLATE utf8_bin,
  `btn_style` char(50) COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

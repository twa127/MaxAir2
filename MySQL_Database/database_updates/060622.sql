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
  `btn_primary` char(50) COLLATE utf8_bin,
  `btn_size` tinyint(4) NOT NULL,
  `color` char(50) COLLATE utf8_bin,
  `tile_size` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (1,0,0,'Blue Left','left','bg-blue','text-white','border-blue','card-footer-blue','btn-bm-blue','btn-primary-blue','0','blue','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (2,0,0,'Blue Center','center','bg-blue','text-white','border-blue','card-footer-blue','btn-bm-blue','btn-primary-blue','0','blue','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (3,0,0,'Orange Left','left','bg-orange','text-white','border-orange','card-footer-orange','btn-bm-orange','btn-primary-orange','0','orange','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (4,0,0,'Orange Center','center','bg-orange','text-white','border-orange','card-footer-orange','btn-bm-orange','btn-primary-orange','0','orange','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (5,0,0,'Red Left','left','bg-red','text-white','border-red','card-footer-red','btn-bm-red','btn-primary-red','0','red','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (6,0,0,'Red Center','center','bg-red','text-white','border-red','card-footer-red','btn-bm-red','btn-primary-red','0','red','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (7,0,0,'Amber Left','left','bg-amber','text-white','border-amber','card-footer-amber','btn-bm-amber','btn-primary-amber','0','amber','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (8,0,0,'Amber Center','center','bg-amber','text-white','border-amber','card-footer-amber','btn-bm-amber','btn-primary-amber','0','amber','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (9,0,0,'Violet Left','left','bg-violet','text-white','border-violet','card-footer-violet','btn-bm-violet','btn-primary-violet','0','violet','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (10,0,0,'Violet Center','center','bg-violet','text-white','border-violet','card-footer-violet','btn-bm-violet','btn-primary-violet','0','violet','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (11,0,0,'Teal Left','left','bg-teal','text-white','border-teal','card-footer-teal','btn-bm-teal','btn-primary-teal','0','teal','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (12,0,0,'Teal Center','center','bg-teal','text-white','border-teal','card-footer-teal','btn-bm-teal','btn-primary-teal','0','teal','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (13,0,0,'Dark Left','left','bg-black','text-white','border-black','card-footer-black','btn-bm-black','btn-primary-black','0','black','0');
INSERT INTO `theme`(`id`, `sync`, `purge`, `name`, `row_justification`, `background_color`, `text_color`, `border_color`, `footer_color`, `btn_style`, `btn_primary`, `btn_size`, `color`, `tile_size`)
VALUES (14,0,0,'Dark Center','center','bg-black','text-white','border-black','card-footer-black','btn-bm-black','btn-primary-black','0','black','0');

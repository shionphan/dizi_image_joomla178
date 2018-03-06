DROP TABLE IF EXISTS `#__di_images`;
CREATE TABLE IF NOT EXISTS `#__di_images` (
  `object_image_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` double(10,0) DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `featured` tinyint(1) DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(10) unsigned DEFAULT NULL,
  `link` text,
  `link_target` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`object_image_id`),
  KEY `object_id_x` (`object_id`),
  KEY `state_x` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `#__di_images_sizes`;
CREATE TABLE IF NOT EXISTS `#__di_images_sizes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `indent` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `height` int(10) NOT NULL DEFAULT '0',
  `width` int(10) NOT NULL DEFAULT '0',
  `crop` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `template_id_x` (`template_id`),
  KEY `indent_x` (`indent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `#__di_images_sizes` (`id`, `template_id`, `indent`, `height`, `width`, `crop`) VALUES
(101, 'default', 'thumb', 100, 1000, '0'),
(102, 'default', 'regular', 250, 250, '1'),
(103, 'default', 'zoomed', 2000, 2000, '0');
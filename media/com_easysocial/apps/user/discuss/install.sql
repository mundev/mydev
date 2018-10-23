CREATE TABLE IF NOT EXISTS `#__discuss_attachments_tmp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` text NOT NULL,
  `title` text NOT NULL,
  `mime` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
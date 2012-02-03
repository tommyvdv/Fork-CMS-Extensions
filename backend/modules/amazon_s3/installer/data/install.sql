CREATE TABLE `amazon_s3_cronjobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` text COLLATE utf8_unicode_ci NOT NULL,
  `full_path` text COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `action` enum('put','delete') COLLATE utf8_unicode_ci NOT NULL,
  `location` enum('s3','local') COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `num_tries` int(11) NOT NULL,
  `last_tried_on` datetime DEFAULT NULL,
  `created_on` datetime NOT NULL,
  `execute_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `photogallery_albums` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'The real post id',
  `user_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `set_id` int(11) DEFAULT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `introduction` text COLLATE utf8_unicode_ci,
  `text` text COLLATE utf8_unicode_ci,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  `publish_on` datetime NOT NULL,
  `hidden` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `show_in_albums` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `num_images` int(11) NOT NULL,
  `num_images_hidden` int(11) NOT NULL,
  `num_images_not_hidden` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `new_from` datetime DEFAULT NULL,
  `new_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status_language_hidden` (`language`,`hidden`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `photogallery_categories_albums`
--

CREATE TABLE `photogallery_categories_albums` (
  `category_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `photogallery_categories`
--

CREATE TABLE `photogallery_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `meta_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photogallery_extras`
--

CREATE TABLE `photogallery_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kind` enum('widget','module') COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `allow_delete` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photogallery_extras_ids`
--

CREATE TABLE `photogallery_extras_ids` (
  `album_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  `modules_extra_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photogallery_extras_resolutions`
--

CREATE TABLE `photogallery_extras_resolutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extra_id` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `method` enum('crop','resize') COLLATE utf8_unicode_ci NOT NULL,
  `kind` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photogallery_sets`
--

CREATE TABLE `photogallery_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `num_images` int(11) NOT NULL,
  `num_albums` int(11) NOT NULL,
  `num_images_hidden` int(11) NOT NULL,
  `num_images_not_hidden` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photogallery_sets_images`
--

CREATE TABLE `photogallery_sets_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_id` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `original_filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sequence` int(11) NOT NULL,
  `hidden` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `photogallery_sets_images_content`
--

CREATE TABLE `photogallery_sets_images_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_image_id` int(11) NOT NULL,
  `set_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title_hidden` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Y',
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `meta_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

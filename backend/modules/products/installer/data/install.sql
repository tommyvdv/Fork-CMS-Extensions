CREATE TABLE `products_products` (
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
-- Table structure for table `products_categories_products`
--

CREATE TABLE `products_categories_products` (
  `category_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `products_categories`
--

CREATE TABLE `products_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `meta_id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products_extras`
--

CREATE TABLE `products_extras` (
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
-- Table structure for table `products_extras_ids`
--

CREATE TABLE `products_extras_ids` (
  `album_id` int(11) NOT NULL,
  `extra_id` int(11) NOT NULL,
  `modules_extra_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products_extras_resolutions`
--

CREATE TABLE `products_extras_resolutions` (
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
-- Table structure for table `products_sets`
--

CREATE TABLE `products_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `num_images` int(11) NOT NULL,
  `num_products` int(11) NOT NULL,
  `num_images_hidden` int(11) NOT NULL,
  `num_images_not_hidden` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products_sets_images`
--

CREATE TABLE `products_sets_images` (
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
-- Table structure for table `products_sets_images_content`
--

CREATE TABLE `products_sets_images_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_image_id` int(11) NOT NULL,
  `set_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `language` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `meta_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `edited_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

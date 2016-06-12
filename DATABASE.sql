-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 04, 2015 at 02:32 PM
-- Server version: 5.5.40
-- PHP Version: 5.4.33

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tso`
--

-- --------------------------------------------------------

--
-- Table structure for table `README`
--

CREATE TABLE IF NOT EXISTS `README` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `README`
--

INSERT INTO `README` (`id`, `name`, `description`) VALUES
(1, 'README', 'shows naming conventions used for tables.'),
(2, 'ts_', 'table prefix for all base tables.'),
(3, 'ts_mind_', 'link table prefix for [remember] and [forget] functionality. '),
(4, 'ts_link_', 'link table prefix for a generic table.'),
(5, 'ts_index_', 'link_table prefix for rebuildable helper tables.'),
(6, 'ALPHABETICAL', 'all linked fields must be in the name for linked tables, are listed alphabetically, and separated by _'),
(7, 'ONE WORD', 'all base table names are only 1 word long (alphanumeric all lowercase) no exeptions. This is important so that we don''t abuse the _ separator.'),
(8, '_more', 'suffix to extend superficial values to a table. (values that will only be used in a particular instance)'),
(9, 'COMMENT', 'Do not retrieve use or even rely on a column named comment. It is only a guide for special fields in the database.'),
(10, 'ts_gauge_', 'link table prefix for rebuildable calculations');

-- --------------------------------------------------------

--
-- Table structure for table `ts_active_offer_user`
--

CREATE TABLE IF NOT EXISTS `ts_active_offer_user` (
  `offer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  KEY `offer_id` (`offer_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_active_offer_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_boolean`
--

CREATE TABLE IF NOT EXISTS `ts_boolean` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='intentionally not really boolean values' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ts_boolean`
--

INSERT INTO `ts_boolean` (`id`, `name`) VALUES
(1, 'true'),
(2, 'false');

-- --------------------------------------------------------

--
-- Table structure for table `ts_channel`
--

CREATE TABLE IF NOT EXISTS `ts_channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timeframe_id` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `offset` int(11) NOT NULL,
  `value` double NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`modified`),
  KEY `parent_id` (`parent_id`),
  KEY `timeframe_id` (`timeframe_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ts_channel`
--

INSERT INTO `ts_channel` (`id`, `parent_id`, `user_id`, `timeframe_id`, `name`, `description`, `offset`, `value`, `modified`, `active`) VALUES
(1, 0, 132, 0, '<|*|>', '<|*|>', 30, 30, '2014-06-06 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_class`
--

CREATE TABLE IF NOT EXISTS `ts_class` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ts_class`
--

INSERT INTO `ts_class` (`id`, `name`) VALUES
(1, 'manual');

-- --------------------------------------------------------

--
-- Table structure for table `ts_contact`
--

CREATE TABLE IF NOT EXISTS `ts_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link` (`user_id`,`name`),
  KEY `modified` (`modified`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=839 ;

--
-- Dumping data for table `ts_contact`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_cost`
--

CREATE TABLE IF NOT EXISTS `ts_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `value` double NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`channel_id`,`modified`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ts_cost`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_cycle`
--

CREATE TABLE IF NOT EXISTS `ts_cycle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_id` int(11) NOT NULL,
  `timeframe_id` tinyint(4) NOT NULL,
  `start` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`),
  KEY `channel_id` (`channel_id`),
  KEY `start` (`start`),
  KEY `timeframe_id` (`timeframe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ts_cycle`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_decision`
--

CREATE TABLE IF NOT EXISTS `ts_decision` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ts_decision`
--

INSERT INTO `ts_decision` (`id`, `name`, `value`) VALUES
(1, 'decision_approve', 1),
(2, 'decision_neutral', 0),
(3, 'decision_disapprove', -1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_dialect`
--

CREATE TABLE IF NOT EXISTS `ts_dialect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ts_dialect`
--

INSERT INTO `ts_dialect` (`id`, `user_id`, `code`, `name`, `description`, `modified`, `active`) VALUES
(2, 132, 'en', 'English', 'Only language totally supported.  In the future other languages will be supported.  Dialect is used to compute value.', '2007-10-24 20:58:14', 1),
(3, 132, 'jp', '日本語', 'Japanese!  Is this site really multilingual?', '2008-06-07 02:06:51', 1),
(4, 133, 'de', 'Deutcsh', 'unvollstaendig', '2008-10-04 13:40:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_direction`
--

CREATE TABLE IF NOT EXISTS `ts_direction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ts_direction`
--

INSERT INTO `ts_direction` (`id`, `name`) VALUES
(1, 'direction_to'),
(2, 'direction_from');

-- --------------------------------------------------------

--
-- Table structure for table `ts_display`
--

CREATE TABLE IF NOT EXISTS `ts_display` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `ts_display`
--

INSERT INTO `ts_display` (`id`, `name`) VALUES
(2, 'display_width_320_pixels'),
(3, 'display_width_480_pixels'),
(4, 'display_width_1024_pixels'),
(6, 'display_select_none'),
(7, 'display_select_default');

-- --------------------------------------------------------

--
-- Table structure for table `ts_doc`
--

CREATE TABLE IF NOT EXISTS `ts_doc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_id` (`page_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `ts_doc`
--

INSERT INTO `ts_doc` (`id`, `page_id`, `modified`) VALUES
(1, 30, '2014-11-11 00:00:00'),
(2, 31, '2012-03-31 00:00:00'),
(3, 32, '2010-07-05 00:00:00'),
(4, 34, '2013-10-29 00:00:00'),
(5, 35, '2009-11-22 00:00:00'),
(6, 357, '2009-11-22 00:00:00'),
(7, 306, '2009-11-22 00:00:00'),
(8, 352, '2010-11-25 00:00:00'),
(9, 33, '2009-11-22 00:00:00'),
(10, 358, '2009-11-22 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `ts_element`
--

CREATE TABLE IF NOT EXISTS `ts_element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=757 ;

--
-- Dumping data for table `ts_element`
--

INSERT INTO `ts_element` (`id`, `name`) VALUES
(1, 'generic_list_result_process'),
(2, 'generic_list_search_process'),
(3, 'generic_warp_process'),
(12, 'home_area'),
(13, 'incident_add_edit'),
(14, 'incident_add_edit_process'),
(15, 'incident_list'),
(16, 'incident_select'),
(17, 'incident_select_process'),
(18, 'item_add_edit'),
(19, 'item_add_edit_process'),
(20, 'item_list'),
(21, 'transfer_list'),
(22, 'translation_add_edit'),
(23, 'translation_add_edit_process'),
(24, 'translation_list'),
(25, 'location_add_edit'),
(26, 'location_add_edit_process'),
(27, 'location_list'),
(28, 'lock_set'),
(29, 'lock_set_process'),
(30, 'lock_unset_delete_this'),
(31, 'lock_unset_process_delete_this'),
(32, 'login_set'),
(33, 'login_set_process'),
(34, 'login_list'),
(36, 'message_area'),
(37, 'news_add_edit'),
(38, 'news_add_edit_process'),
(39, 'news_list'),
(40, 'note_add_edit'),
(41, 'note_add_edit_process'),
(42, 'note_list'),
(43, 'offer_add_edit'),
(44, 'offer_add_edit_process'),
(45, 'offer_list'),
(46, 'people_area'),
(52, 'selection_delete'),
(53, 'selection_delete_process'),
(54, 'selection_export'),
(55, 'selection_export_process'),
(56, 'selection_forget'),
(57, 'selection_forget_process'),
(58, 'selection_group'),
(59, 'selection_group_process'),
(60, 'selection_remember'),
(61, 'selection_remember_process'),
(62, 'singularity_add_edit'),
(63, 'singularity_add_edit_process'),
(64, 'singularity_list'),
(65, 'singularity_report'),
(66, 'sitemap_doc'),
(552, 'meritopic_name'),
(551, 'incident_name'),
(550, 'note_name'),
(70, 'tag_add_edit'),
(71, 'tag_add_edit_process'),
(72, 'tag_list'),
(73, 'teammate_add_edit'),
(74, 'teammate_add_edit_process'),
(75, 'teammate_list'),
(76, 'team_add_edit'),
(77, 'team_add_edit_process'),
(78, 'team_list'),
(79, 'team_select'),
(80, 'team_select_process'),
(81, 'thing_add_edit'),
(82, 'thing_add_edit_process'),
(83, 'thing_list'),
(84, 'tutorial_doc'),
(85, 'user_add_edit'),
(86, 'user_add_edit_process'),
(549, 'offer_name'),
(88, 'user_info_add_edit_process'),
(89, 'user_list'),
(90, 'user_lock'),
(91, 'user_lock_process'),
(92, 'user_login'),
(93, 'user_login_process'),
(94, 'user_logout'),
(95, 'user_logout_process'),
(96, 'new_area'),
(97, 'user_report'),
(98, 'user_select'),
(99, 'user_select_process'),
(100, 'user_unlock_process'),
(346, 'invite_add_edit'),
(383, 'feature_search'),
(104, ''),
(105, 'about_doc'),
(106, 'category_add_edit'),
(107, 'category_add_edit_process'),
(108, 'category_list'),
(109, 'color_theme'),
(110, 'contact_area'),
(111, 'contact_add_edit'),
(112, 'contact_add_edit_process'),
(113, 'contact_info_add_edit_process'),
(114, 'contact_list'),
(115, 'contact_report'),
(116, 'contact_select'),
(117, 'contact_select_process'),
(118, 'control_area'),
(119, 'dialect_add_edit'),
(120, 'dialect_add_edit_process'),
(121, 'dialect_list'),
(122, 'dialect_set'),
(123, 'dialect_set_process'),
(124, 'disclaimer_doc'),
(125, 'doc_area'),
(126, 'donate_doc'),
(343, 'invite_user_name'),
(342, 'remember_login'),
(130, 'faq_doc'),
(131, 'feedback_add_edit'),
(132, 'feedback_add_edit_process'),
(133, 'feedback_list'),
(134, 'category_name'),
(135, 'contact_description'),
(341, 'ignore_process'),
(137, 'contact_name'),
(138, 'contact_note_name'),
(344, 'invite_password'),
(140, 'dialect_description'),
(141, 'dialect_name'),
(142, 'dialect_type'),
(143, 'dialect_type_id'),
(144, 'dialect_value'),
(147, 'incident_description'),
(148, 'incident_id'),
(257, 'phase_name'),
(150, 'status_name'),
(151, 'keyword'),
(152, 'location_latitude'),
(153, 'location_longitude'),
(154, 'location_name'),
(155, 'news_description'),
(156, 'news_name'),
(157, 'offer_description'),
(159, 'right_name'),
(160, 'success_description'),
(161, 'tag_description'),
(162, 'team_description'),
(163, 'team_email'),
(164, 'team_name'),
(165, 'team_website'),
(166, 'thing_name'),
(167, 'user_description'),
(168, 'accept_usage_policy'),
(169, 'user_email'),
(170, 'user_name'),
(171, 'user_password_unencrypted'),
(172, 'user_password_unencrypted_again'),
(173, 'user_website'),
(174, 'go_back'),
(175, 'element_name'),
(176, 'translation_name'),
(177, 'select'),
(178, 'add'),
(179, 'edit'),
(180, 'select_contact'),
(181, 'add_mailbox'),
(182, 'add_webspot'),
(183, 'add_note'),
(289, 'contact_view'),
(187, 'select_incident'),
(188, 'add_feedback'),
(189, 'note_description'),
(190, 'select_team'),
(191, 'add_teammate'),
(192, 'grade_merit_none'),
(193, 'grade_merit_quarter'),
(194, 'grade_merit_half'),
(195, 'grade_merit_triquarter'),
(196, 'grade_merit_full'),
(197, 'user_name_fixed'),
(198, 'grade_name'),
(199, 'singularity_id'),
(200, 'item_description'),
(201, 'set'),
(202, 'remember_location'),
(203, 'forget_location'),
(481, 'other_area'),
(205, 'remember_category'),
(206, 'forget_category'),
(207, 'add_user'),
(208, 'recover_login'),
(209, 'feedback_description'),
(210, 'category_parent_name'),
(211, 'select_location'),
(212, 'range_name'),
(213, 'order_name'),
(214, 'right_admin'),
(215, 'right_member'),
(216, 'right_pending'),
(217, 'right_banned'),
(218, 'new_report'),
(219, 'add_category'),
(220, 'add_contact'),
(221, 'add_dialect'),
(223, 'add_thing'),
(224, 'add_team'),
(225, 'add_tag'),
(226, 'add_success'),
(227, 'add_singularity'),
(229, 'add_offer'),
(230, 'add_news'),
(231, 'set_login'),
(232, 'add_location'),
(233, 'add_item'),
(234, 'add_incident'),
(235, 'add_translation'),
(236, 'welcome_message'),
(246, 'select_category'),
(237, 'location_select'),
(238, 'category_select'),
(239, 'location_name_delete_this'),
(240, 'status_available'),
(241, 'status_neutral'),
(242, 'status_wanted'),
(243, 'phase_closed'),
(244, 'phase_open'),
(245, 'phase_neutral'),
(251, 'unset_login'),
(250, 'search'),
(247, 'generic_unset_process'),
(248, 'unset_lock'),
(249, 'unselect_location'),
(252, 'range_005_mile'),
(253, 'range_010_mile'),
(254, 'range_025_mile'),
(255, 'range_050_mile'),
(256, 'range_100_mile'),
(258, 'unselect_category'),
(259, 'theme_set_process'),
(260, 'select_tag'),
(261, 'select_thing'),
(262, 'select_dialect'),
(263, 'thing_select'),
(264, 'unselect_thing'),
(265, 'dialect_select'),
(266, 'unselect_dialect'),
(267, 'dialect_select_process'),
(268, 'thing_select_process'),
(269, 'selection_action'),
(270, 'selection_action_process'),
(271, 'locationmind_list'),
(272, 'categorymind_list'),
(274, 'location_select_process'),
(275, 'category_select_process'),
(276, 'lock_user_name'),
(277, 'lock_contact_name'),
(279, 'lock_team_name'),
(280, 'lock_location_name'),
(281, 'lock_range_name'),
(283, 'range_000_mile'),
(284, 'direction_to'),
(285, 'direction_from'),
(286, 'direction_name'),
(288, 'incident_view'),
(290, 'singularity_view'),
(291, 'location_view'),
(292, 'category_view'),
(294, 'team_view'),
(296, 'item_list_rss'),
(297, 'set_lock'),
(299, 'lofi_theme'),
(300, 'hifi_theme'),
(301, 'transaction_complete'),
(302, 'index.php'),
(303, 'merit_doc'),
(304, 'meritype_good'),
(305, 'meritype_bad'),
(306, 'meritype_monetary'),
(307, 'meritype_identity'),
(308, 'meritopic_list'),
(309, 'meripost_list'),
(310, 'meritype_name'),
(311, 'meritopic_id'),
(312, 'add_meritopic'),
(313, 'add_meripost'),
(314, 'meritopic_add_edit'),
(315, 'meripost_add_edit'),
(316, 'meritopic_description'),
(317, 'meripost_description'),
(318, 'select_meritopic'),
(319, 'meritopic_select'),
(320, 'meritopic_select_process'),
(321, 'meritopic_add_edit_process'),
(322, 'meripost_add_edit_process'),
(323, 'meritopic_view'),
(324, 'first_page'),
(325, 'previous_page'),
(326, 'next_page'),
(327, 'last_page'),
(328, 'add_more'),
(329, 'result_heading'),
(330, 'most_recent'),
(331, 'asearch_on'),
(332, 'asearch_off'),
(333, 'unset_result_element_name'),
(334, 'edit_user'),
(335, 'login_recover'),
(336, 'login_recover_process'),
(337, 'launch_js'),
(338, 'launch'),
(339, 'launcher'),
(340, 'main'),
(347, 'invite_add_edit_process'),
(348, 'used'),
(349, 'not_used'),
(350, 'add_invite'),
(351, 'add_invited'),
(352, 'invited_add_edit_process'),
(353, 'invited_add_edit'),
(354, 'invited_list'),
(355, 'is_used'),
(356, 'true'),
(357, 'false'),
(358, 'recover_rss'),
(553, 'teammind_list'),
(360, 'rss_recover_process'),
(361, 'recover'),
(362, 'login_unset_process'),
(363, 'lock_unset_process'),
(364, 'theme_unset_process'),
(365, 'dialect_unset_process'),
(366, 'notify_offer_received'),
(368, 'notify_transfer_received'),
(372, 'transfer_add_edit'),
(373, 'add_transfer'),
(374, 'transfer_add_edit_process'),
(375, 'transfer_description'),
(376, 'feature_cornucopia'),
(377, 'adder_report'),
(378, 'none'),
(379, 'delete'),
(380, 'import'),
(381, 'export'),
(382, 'invite_email'),
(384, 'feature_lock'),
(417, 'no_result'),
(418, 'browse_all'),
(419, 'text_style'),
(479, 'locationmate_add_edit'),
(480, 'categorymate_add_edit'),
(482, 'download_doc'),
(483, 'profile'),
(484, 'notify_teammate_received'),
(485, 'theme_set'),
(486, 'theme_name'),
(487, 'display_name'),
(488, 'theme_red'),
(489, 'theme_orange'),
(490, 'theme_yellow'),
(491, 'theme_green'),
(492, 'theme_blue'),
(493, 'theme_purple'),
(494, 'theme_pink'),
(495, 'theme_black'),
(496, 'theme_brown'),
(497, 'theme_gray'),
(498, 'theme_select_none'),
(499, 'display_width_100_percent_narrow'),
(500, 'display_width_320_pixels'),
(501, 'display_width_480_pixels'),
(502, 'display_width_1024_pixels'),
(503, 'display_set'),
(504, 'display_set_process'),
(505, 'welcome_to_trade_and_share'),
(506, 'team_required_name'),
(507, 'team_qualified_name'),
(508, 'landingmap_doc'),
(509, 'notation_doc'),
(510, 'recover_area'),
(511, 'set_area'),
(512, 'on'),
(513, 'off'),
(514, 'feed_recover_process'),
(515, 'generic_rss'),
(516, 'feature_feed'),
(517, 'page_name'),
(519, 'feed_add_edit'),
(518, 'feed_list'),
(520, 'feed_add_edit_process'),
(521, 'item_id'),
(523, 'login_id'),
(524, 'news_id'),
(525, 'feedback_id'),
(526, 'offer_id'),
(527, 'transfer_id'),
(528, 'contact_id'),
(529, 'note_id'),
(532, 'team_id'),
(533, 'teammate_id'),
(534, 'location_id'),
(535, 'locationmate_id'),
(536, 'user_id'),
(537, 'invited_id'),
(538, 'tag_id'),
(539, 'category_id'),
(540, 'categorymate_id'),
(541, 'thing_id'),
(542, 'feed_id'),
(543, 'meripost_id'),
(544, 'access_public_web'),
(545, 'access_user_all'),
(546, 'access_team_intra'),
(547, 'access_user_inter'),
(548, 'access_user_author'),
(554, 'display_width_100_percent_wide'),
(555, 'profile_add_edit'),
(556, 'profile_add_edit_process'),
(557, 'trailer_doc'),
(558, 'login_user_name'),
(559, 'autouser_ajax'),
(560, 'autocontact_ajax'),
(561, 'autodrop_js'),
(562, 'load_set'),
(563, 'config_set'),
(564, 'load_javascript'),
(565, 'config_set_process'),
(566, 'load_set_process'),
(567, 'cuser_name'),
(568, 'ucontact_name'),
(569, 'feed_name'),
(570, 'enabled'),
(571, 'more'),
(572, 'login_user_password_unencrypted'),
(573, 'contact_user_mixed'),
(574, 'lock_contact_user_mixed'),
(575, 'autocontact_autouser_ajax'),
(576, 'feed_recover'),
(577, 'feed_query'),
(578, 'welcome'),
(579, 'tslist_doc'),
(580, 'top_report'),
(581, 'feature_minnotify'),
(582, 'config_report'),
(583, 'metail_list'),
(584, 'metail_add_edit'),
(585, 'add_metail'),
(586, 'metail_description'),
(587, 'metail_add_edit_process'),
(588, 'user_view'),
(589, 'memorize'),
(590, 'search_content_box'),
(591, 'search_menu_1'),
(592, 'search_menu_2'),
(593, 'result_content_box'),
(594, 'result_menu_1'),
(595, 'result_menu_2'),
(596, 'view_content_box'),
(597, 'view_menu_1'),
(598, 'view_menu_2'),
(599, 'generic_atom'),
(600, 'display_select_none'),
(601, 'contact_mixed'),
(602, 'user_mixed'),
(603, 'categorization_list'),
(604, 'categorization_add_edit'),
(605, 'categorization_description'),
(606, 'categorization_add_edit_process'),
(607, 'search_report'),
(608, 'kind_name'),
(609, 'kind_name_name'),
(610, 'translation_description'),
(611, 'mind_name'),
(612, 'tag_translation_name'),
(613, 'tag_name'),
(614, 'unset'),
(615, 'add_feed'),
(616, 'extra'),
(617, 'everything'),
(618, 'expand_all'),
(619, 'less'),
(620, 'known'),
(621, 'kind_uid'),
(622, 'container_uid'),
(623, 'direction_uid'),
(624, 'display_uid'),
(625, 'grade_uid'),
(626, 'meritype_uid'),
(627, 'page_uid'),
(628, 'phase_uid'),
(629, 'range_uid'),
(630, 'status_uid'),
(631, 'tag_uid'),
(632, 'theme_uid'),
(633, 'category_uid'),
(634, 'element_uid'),
(635, 'item_uid'),
(636, 'news_uid'),
(638, 'metail_uid'),
(639, 'login_uid'),
(640, 'incident_uid'),
(641, 'feedback_uid'),
(642, 'offer_uid'),
(643, 'transfer_uid'),
(644, 'contact_uid'),
(645, 'note_uid'),
(648, 'feed_uid'),
(649, 'team_uid'),
(650, 'teammate_uid'),
(651, 'location_uid'),
(652, 'user_uid'),
(653, 'invited_uid'),
(654, 'minder_uid'),
(655, 'translation_uid'),
(656, 'meritopic_uid'),
(657, 'meripost_uid'),
(658, 'reedit'),
(729, 'translate'),
(728, 'jargon_uid'),
(726, 'is_empty'),
(724, 'find_meripost'),
(717, 'access_mixed'),
(718, 'error_does_not_exist'),
(721, 'submit'),
(725, 'default_boolean_name'),
(723, 'minder_kind_name'),
(722, 'translation_kind_name'),
(719, 'error_does_exist'),
(720, 'error_field_missing'),
(727, 'error'),
(685, 'feed_description'),
(686, 'collapse_all'),
(687, 'email_sent'),
(688, 'set_again'),
(689, 'send'),
(690, 'send_more'),
(692, 'find_item'),
(693, 'find_news'),
(695, 'find_metail'),
(696, 'find_visit'),
(697, 'find_incident'),
(698, 'find_feedback'),
(699, 'find_offer'),
(700, 'find_transfer'),
(701, 'find_contact'),
(702, 'find_note'),
(704, 'find_feed'),
(705, 'find_team'),
(706, 'find_teammate'),
(707, 'find_location'),
(708, 'find_invited'),
(709, 'find_user'),
(710, 'find_category'),
(711, 'find_tag'),
(712, 'find_minder'),
(713, 'find_translation'),
(714, 'find_meritopic'),
(715, 'find_meritype'),
(716, 'find_login'),
(730, 'find_jargon'),
(731, 'background_theme_name'),
(732, 'launcher_theme_name'),
(733, 'search_mixed'),
(734, 'view'),
(735, 'remember'),
(736, 'forget'),
(737, 'default'),
(738, 'tag_path'),
(739, 'parent_tag_path'),
(740, 'accept_friend'),
(742, 'access_user_intra'),
(743, 'redirect'),
(744, 'new_form'),
(745, 'add_login'),
(751, 'page_parent'),
(752, 'page_next'),
(753, 'page_previous'),
(754, 'page_first'),
(755, 'page_last'),
(756, 'list');

-- --------------------------------------------------------

--
-- Table structure for table `ts_feed`
--

CREATE TABLE IF NOT EXISTS `ts_feed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `dialect_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `query` varchar(1020) NOT NULL,
  `modified` datetime NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`),
  KEY `page_id` (`page_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

--
-- Dumping data for table `ts_feed`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_feedback`
--

CREATE TABLE IF NOT EXISTS `ts_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `incident_id` (`incident_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1996 ;

--
-- Dumping data for table `ts_feedback`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_file`
--

CREATE TABLE IF NOT EXISTS `ts_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `ts_file`
--

INSERT INTO `ts_file` (`id`, `path`, `name`) VALUES
(2, 'list/v1/x/', 'layer.php'),
(3, 'list/v1/x/', 'area.php'),
(12, 'list/v1/x/', 'css.php'),
(5, 'list/v1/x/', 'process.php'),
(6, 'list/v1/x/', 'html.php'),
(11, 'list/v1/x/', 'custom.php'),
(14, 'list/v1/x/', 'page.php'),
(19, 'list/v1/x/', 'xml.php'),
(20, 'list/v1/x/', 'js.php'),
(26, 'list/v1/x/', 'profile.php'),
(27, 'list/v1/x/', 'ajax.php');

-- --------------------------------------------------------

--
-- Table structure for table `ts_filer`
--

CREATE TABLE IF NOT EXISTS `ts_filer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `md5` char(32) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `byte` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `default` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `ts_filer`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_gauge_renewal`
--

CREATE TABLE IF NOT EXISTS `ts_gauge_renewal` (
  `renewal_id` int(11) NOT NULL,
  `renewal_value` double NOT NULL,
  `rating_value` double NOT NULL,
  PRIMARY KEY (`renewal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_gauge_renewal`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_grade`
--

CREATE TABLE IF NOT EXISTS `ts_grade` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ts_grade`
--

INSERT INTO `ts_grade` (`id`, `name`, `value`) VALUES
(1, 'grade_merit_none', 0),
(2, 'grade_merit_quarter', 1),
(3, 'grade_merit_half', 2),
(4, 'grade_merit_triquarter', 3),
(5, 'grade_merit_full', 4);

-- --------------------------------------------------------

--
-- Table structure for table `ts_group`
--

-- CREATE TABLE IF NOT EXISTS `ts_group` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `user_id` int(11) NOT NULL,
--   `name` varchar(255) NOT NULL DEFAULT '',
--   `description` varchar(255) NOT NULL DEFAULT '',
--   `modified` datetime NOT NULL,
--   `active` tinyint(1) NOT NULL,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `link` (`user_id`,`name`),
--   KEY `user_id` (`user_id`),
--   KEY `modified` (`modified`)
-- ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `ts_group`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_incident`
--

CREATE TABLE IF NOT EXISTS `ts_incident` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `phase_id` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`),
  KEY `phase_id` (`phase_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1331 ;

--
-- Dumping data for table `ts_incident`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_index_invited_user`
--

CREATE TABLE IF NOT EXISTS `ts_index_invited_user` (
  `user_id` int(11) NOT NULL,
  `invited_id` int(11) NOT NULL,
  UNIQUE KEY `invited_user` (`user_id`,`invited_id`),
  KEY `user_id` (`user_id`),
  KEY `invite_id` (`invited_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_index_invited_user`
--

INSERT INTO `ts_index_invited_user` (`user_id`, `invited_id`) VALUES
(111, 76);

-- --------------------------------------------------------

--
-- Table structure for table `ts_index_offer_user`
--

CREATE TABLE IF NOT EXISTS `ts_index_offer_user` (
  `offer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `offer_user` (`offer_id`,`user_id`),
  KEY `offer_id` (`offer_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_index_offer_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_index_rating_user`
--

CREATE TABLE IF NOT EXISTS `ts_index_rating_user` (
  `rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `rating_user` (`rating_id`,`user_id`),
  KEY `rating_id` (`rating_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_index_rating_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_index_tag`
--

CREATE TABLE IF NOT EXISTS `ts_index_tag` (
  `tag_id` int(11) NOT NULL,
  `tag_path` varchar(255) NOT NULL,
  UNIQUE KEY `tag_id` (`tag_id`,`tag_path`),
  KEY `tag_id_2` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_index_tag`
--

INSERT INTO `ts_index_tag` (`tag_id`, `tag_path`) VALUES
(1, '<|!|>');

-- --------------------------------------------------------

--
-- Table structure for table `ts_index_transfer_user`
--

CREATE TABLE IF NOT EXISTS `ts_index_transfer_user` (
  `transfer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  UNIQUE KEY `transfer_user` (`transfer_id`,`user_id`),
  KEY `transfer_id` (`transfer_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_index_transfer_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_invite`
--

CREATE TABLE IF NOT EXISTS `ts_invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `used` tinyint(4) NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=305 ;

--
-- Dumping data for table `ts_invite`
--

INSERT INTO `ts_invite` (`id`, `user_id`, `email`, `password`, `modified`, `used`, `active`) VALUES
(291, 111, 'root@localhost', 'peer_authenticated', '2011-07-21 11:20:22', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_invited`
--

CREATE TABLE IF NOT EXISTS `ts_invited` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invite_id` int(11) NOT NULL,
  `source_user_id` int(11) NOT NULL,
  `destination_user_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `source_user_id` (`source_user_id`,`destination_user_id`,`modified`),
  KEY `invite_id` (`invite_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=86 ;

--
-- Dumping data for table `ts_invited`
--

INSERT INTO `ts_invited` (`id`, `invite_id`, `source_user_id`, `destination_user_id`, `modified`, `active`) VALUES
(76, 291, 111, 111, '2011-07-21 11:20:22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_item`
--

CREATE TABLE IF NOT EXISTS `ts_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3803 ;

--
-- Dumping data for table `ts_item`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_kind`
--

CREATE TABLE IF NOT EXISTS `ts_kind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `translation` tinyint(1) NOT NULL,
  `minder` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `ts_kind`
--

INSERT INTO `ts_kind` (`id`, `name`, `translation`, `minder`) VALUES
(1, 'kind', 1, 2),
(18, 'decision', 1, 2),
(3, 'direction', 1, 2),
(4, 'display', 1, 2),
(5, 'grade', 1, 2),
(6, 'meritype', 1, 2),
(7, 'page', 1, 2),
(8, 'phase', 1, 2),
(9, 'range', 1, 2),
(10, 'status', 1, 2),
(11, 'tag', 1, 1),
(12, 'theme', 1, 2),
(14, 'element', 1, 2),
(15, 'location', 2, 1),
(16, 'team', 2, 1),
(17, 'boolean', 1, 2),
(19, 'channel', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ts_link_contact_group`
--

-- CREATE TABLE IF NOT EXISTS `ts_link_contact_group` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `contact_id` int(11) NOT NULL,
--   `group_id` int(11) NOT NULL,
--   `modified` datetime NOT NULL,
--   `active` tinyint(1) NOT NULL,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `link` (`contact_id`,`group_id`),
--   KEY `contact_id` (`contact_id`),
--   KEY `group_id` (`group_id`),
--   KEY `modified` (`modified`)
-- ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `ts_link_contact_group`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_link_contact_user`
--

CREATE TABLE IF NOT EXISTS `ts_link_contact_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link` (`user_id`,`contact_id`),
  KEY `user_id` (`user_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=199 ;

--
-- Dumping data for table `ts_link_contact_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_link_tag`
--

CREATE TABLE IF NOT EXISTS `ts_link_tag` (
  `tag_id` int(11) NOT NULL,
  KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_link_tag`
--

-- --------------------------------------------------------

--
-- Table structure for table `ts_link_team_user`
--

CREATE TABLE IF NOT EXISTS `ts_link_team_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `team_id` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_team` (`user_id`,`team_id`),
  KEY `team_id` (`team_id`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=311 ;

--
-- Dumping data for table `ts_link_team_user`
--

INSERT INTO `ts_link_team_user` (`id`, `user_id`, `team_id`, `modified`, `active`) VALUES
(250, 111, 103, '2011-07-21 11:20:22', 1),
(251, 111, 175, '2011-07-21 11:20:22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_location`
--

CREATE TABLE IF NOT EXISTS `ts_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `latitude` double NOT NULL DEFAULT '0',
  `longitude` double NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=53 ;

--
-- Dumping data for table `ts_location`
--

INSERT INTO `ts_location` (`id`, `user_id`, `latitude`, `longitude`, `name`, `modified`, `active`) VALUES
(1, 132, 90, 0, '<|?|>', '2007-08-18 09:49:12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_lock`
--

CREATE TABLE IF NOT EXISTS `ts_lock` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ts_lock`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_login`
--

CREATE TABLE IF NOT EXISTS `ts_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `when` (`when`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48416 ;

--
-- Dumping data for table `ts_login`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_membership`
--

CREATE TABLE IF NOT EXISTS `ts_membership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `autorenew` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`channel_id`,`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ts_membership`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_meripost`
--

CREATE TABLE IF NOT EXISTS `ts_meripost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `meritopic_id` int(11) NOT NULL,
  `meritype_id` tinyint(4) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `meritopic_id` (`meritopic_id`),
  KEY `meritype_id` (`meritype_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=405 ;

--
-- Dumping data for table `ts_meripost`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_meritopic`
--

CREATE TABLE IF NOT EXISTS `ts_meritopic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=85 ;

--
-- Dumping data for table `ts_meritopic`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_meritype`
--

CREATE TABLE IF NOT EXISTS `ts_meritype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ts_meritype`
--

INSERT INTO `ts_meritype` (`id`, `name`) VALUES
(1, 'meritype_good'),
(2, 'meritype_bad'),
(3, 'meritype_monetary'),
(4, 'meritype_identity');

-- --------------------------------------------------------

--
-- Table structure for table `ts_metail`
--

CREATE TABLE IF NOT EXISTS `ts_metail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`),
  KEY `user_id` (`user_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

--
-- Dumping data for table `ts_metail`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_minder`
--

CREATE TABLE IF NOT EXISTS `ts_minder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `kind_id` int(11) NOT NULL,
  `kind_name_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `kind_id` (`kind_id`),
  KEY `kind_name_id` (`kind_name_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=421 ;

--
-- Dumping data for table `ts_minder`
--

INSERT INTO `ts_minder` (`id`, `user_id`, `kind_id`, `kind_name_id`, `modified`, `active`) VALUES
(110, 111, 11, 1, '2011-07-21 11:20:22', 1),
(185, 111, 15, 1, '2011-07-21 11:20:22', 1),
(328, 111, 16, 103, '2011-07-21 11:20:22', 1),
(329, 111, 16, 175, '2011-07-21 11:20:22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_news`
--

CREATE TABLE IF NOT EXISTS `ts_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`),
  KEY `user_id` (`user_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=190 ;

--
-- Dumping data for table `ts_news`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_note`
--

CREATE TABLE IF NOT EXISTS `ts_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=655 ;

--
-- Dumping data for table `ts_note`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_offer`
--

CREATE TABLE IF NOT EXISTS `ts_offer` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `source_user_id` int(11) NOT NULL,
  `destination_user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`),
  KEY `source_user_id` (`source_user_id`),
  KEY `destination_user_id` (`destination_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7122 ;

--
-- Dumping data for table `ts_offer`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_page`
--

CREATE TABLE IF NOT EXISTS `ts_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `order` tinyint(4) NOT NULL,
  `launch` tinyint(1) NOT NULL,
  `monitor` tinyint(1) NOT NULL,
  `login` tinyint(1) NOT NULL,
  `advanced` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=444 ;

--
-- Dumping data for table `ts_page`
--

INSERT INTO `ts_page` (`id`, `parent_id`, `file_id`, `name`, `order`, `launch`, `monitor`, `login`, `advanced`) VALUES
(11, 236, 2, 'news_list', 3, 1, 1, 1, 2),
(13, 236, 2, 'offer_list', 1, 1, 1, 1, 2),
(18, 62, 2, 'category_list', 5, 1, 1, 1, 1),
(121, 321, 2, 'selection_export', 1, 2, 2, 1, 2),
(22, 236, 2, 'contact_list', 30, 1, 1, 1, 1),
(24, 60, 2, 'user_list', 12, 1, 1, 1, 1),
(25, 64, 2, 'team_list', 6, 1, 1, 1, 1),
(26, 64, 2, 'teammate_list', 7, 1, 1, 1, 1),
(29, 321, 14, 'user_report', 1, 2, 2, 1, 2),
(30, 66, 6, 'about_doc', 30, 1, 1, 2, 1),
(31, 66, 6, 'faq_doc', 40, 1, 1, 2, 1),
(32, 66, 6, 'sitemap_doc', 50, 1, 1, 2, 1),
(33, 66, 6, 'tutorial_doc', 70, 2, 2, 2, 1),
(34, 66, 6, 'disclaimer_doc', 80, 1, 1, 2, 1),
(35, 66, 6, 'donate_doc', 90, 1, 1, 2, 1),
(177, 321, 2, 'tag_edit', 1, 1, 2, 1, 1),
(60, 68, 3, 'home_area', 10, 2, 2, 1, 2),
(61, 321, 3, 'message_area', 20, 2, 2, 1, 2),
(62, 68, 3, 'control_area', 55, 2, 2, 1, 1),
(176, 62, 2, 'tag_list', 20, 1, 1, 1, 1),
(64, 68, 3, 'people_area', 40, 2, 2, 1, 1),
(187, 360, 2, 'lock_set', 1, 1, 2, 1, 1),
(66, 68, 3, 'doc_area', 35, 2, 2, 1, 2),
(68, 321, 11, '', 1, 2, 2, 2, 2),
(188, 321, 2, 'user_lock', 1, 1, 2, 1, 2),
(70, 321, 2, 'offer_edit', 1, 1, 2, 1, 2),
(71, 321, 2, 'incident_edit', 1, 1, 2, 1, 1),
(72, 321, 2, 'news_edit', 1, 1, 2, 1, 1),
(73, 321, 2, 'category_edit', 1, 1, 2, 1, 1),
(74, 321, 2, 'teammate_edit', 1, 1, 2, 1, 1),
(75, 321, 2, 'contact_edit', 1, 1, 2, 1, 1),
(76, 321, 2, 'team_edit', 1, 1, 2, 1, 1),
(79, 321, 2, 'user_login', 1, 1, 2, 1, 2),
(89, 321, 2, 'user_logout', 1, 1, 2, 1, 2),
(93, 321, 2, 'item_edit', 1, 1, 2, 1, 2),
(103, 351, 2, 'incident_list', 10, 1, 1, 1, 1),
(263, 321, 2, 'selection_forget', 1, 2, 2, 1, 2),
(106, 321, 2, 'selection_delete', 1, 2, 2, 1, 2),
(107, 321, 2, 'selection_group', 1, 2, 2, 1, 2),
(265, 321, 2, 'selection_remember', 1, 2, 2, 1, 2),
(125, 236, 2, 'group_list', 50, 1, 1, 1, 1),
(126, 321, 2, 'group_edit', 1, 1, 2, 1, 2),
(131, 351, 2, 'feedback_list', 11, 1, 1, 1, 1),
(132, 321, 2, 'feedback_edit', 1, 1, 2, 1, 1),
(135, 236, 2, 'groupmate_list', 55, 1, 1, 1, 1),
(136, 321, 2, 'groupmate_edit', 1, 1, 2, 1, 2),
(174, 68, 3, 'new_area', 3, 2, 2, 1, 2),
(151, 321, 2, 'minder_edit', 1, 2, 2, 1, 1),
(153, 62, 2, 'minder_list', 70, 1, 1, 1, 1),
(214, 321, 2, 'user_edit', 1, 1, 2, 2, 2),
(216, 321, 2, 'location_edit', 1, 1, 2, 1, 1),
(217, 62, 2, 'location_list', 60, 1, 1, 1, 1),
(218, 60, 2, 'login_list', 22, 1, 1, 1, 1),
(220, 360, 2, 'login_set', 1, 1, 2, 2, 2),
(325, 321, 2, 'invite_edit', 1, 1, 2, 1, 2),
(233, 321, 2, 'note_edit', 1, 1, 2, 1, 2),
(235, 236, 2, 'note_list', 40, 1, 1, 1, 1),
(236, 68, 3, 'contact_area', 30, 2, 2, 1, 2),
(239, 321, 14, 'singularity_report', 1, 2, 2, 1, 2),
(240, 60, 2, 'item_list', 1, 1, 1, 1, 2),
(248, 321, 2, 'dialect_edit', 1, 1, 2, 1, 2),
(250, 62, 2, 'dialect_list', 50, 1, 2, 1, 1),
(254, 321, 14, 'contact_report', 1, 2, 2, 1, 2),
(255, 60, 2, 'transfer_list', 10, 1, 1, 1, 2),
(256, 321, 12, 'color_theme', 1, 1, 2, 2, 2),
(427, 174, 14, 'host_portal', 24, 1, 2, 1, 2),
(428, 321, 11, 'file', 1, 2, 2, 2, 2),
(424, 321, 27, 'autouser_ajax', 1, 2, 2, 2, 2),
(425, 321, 20, 'more_js', 1, 2, 2, 2, 2),
(426, 174, 14, 'guest_portal', 20, 1, 2, 1, 2),
(275, 62, 2, 'translation_list', 40, 1, 1, 1, 1),
(276, 321, 2, 'translation_edit', 1, 1, 2, 1, 2),
(278, 360, 2, 'dialect_set', 1, 1, 2, 2, 2),
(280, 174, 14, 'new_report', 3, 1, 2, 1, 2),
(423, 321, 27, 'autopage_ajax', 1, 2, 2, 2, 2),
(421, 174, 14, 'people_portal', 10, 1, 2, 1, 2),
(422, 174, 14, 'page_portal', 20, 1, 2, 2, 2),
(417, 68, 3, 'ts_area', 2, 2, 2, 1, 2),
(420, 321, 5, 'portal_process', 1, 2, 2, 1, 1),
(289, 321, 14, 'selection_action', 1, 2, 2, 1, 2),
(406, 321, 5, 'recover_process', 1, 2, 2, 2, 1),
(407, 321, 5, 'result_process', 1, 2, 2, 1, 1),
(408, 321, 5, 'search_process', 1, 2, 2, 1, 1),
(297, 321, 2, 'incident_view', 1, 2, 2, 1, 1),
(298, 321, 2, 'contact_view', 1, 2, 2, 1, 1),
(391, 62, 2, 'jargon_list', 30, 1, 1, 1, 1),
(392, 321, 2, 'jargon_edit', 1, 1, 2, 1, 2),
(302, 321, 2, 'group_view', 1, 2, 2, 1, 1),
(303, 321, 2, 'team_view', 1, 2, 2, 1, 1),
(304, 321, 19, 'item_list_rss', 1, 2, 2, 2, 2),
(305, 321, 11, 'index.php', 1, 2, 2, 1, 2),
(306, 66, 6, 'merit_doc', 25, 2, 2, 2, 1),
(308, 351, 2, 'meripost_list', 31, 1, 1, 1, 1),
(307, 351, 2, 'meritopic_list', 30, 1, 1, 1, 1),
(309, 321, 2, 'meritopic_edit', 1, 1, 2, 1, 1),
(310, 321, 2, 'meripost_edit', 1, 1, 2, 1, 1),
(416, 321, 11, 'manager', 1, 1, 2, 1, 2),
(290, 321, 5, 'selection_process', 1, 2, 2, 1, 2),
(415, 321, 2, 'location_view', 1, 2, 2, 1, 1),
(315, 321, 2, 'meritopic_view', 1, 2, 2, 1, 1),
(316, 359, 2, 'login_recover', 1, 1, 2, 2, 2),
(318, 321, 20, 'launch_js', 1, 2, 2, 2, 2),
(321, 321, 11, 'main', 1, 1, 2, 2, 2),
(329, 64, 2, 'invited_list', 123, 1, 1, 1, 1),
(340, 321, 2, 'transfer_edit', 1, 1, 2, 1, 2),
(412, 321, 5, 'user_process', 1, 2, 2, 2, 1),
(342, 321, 14, 'adder_report', 3, 2, 2, 1, 2),
(348, 321, 12, 'text_style', 1, 2, 2, 2, 2),
(349, 321, 2, 'locationmate_edit', 1, 2, 2, 1, 2),
(350, 321, 2, 'categorymate_edit', 1, 2, 2, 1, 2),
(351, 68, 3, 'other_area', 60, 2, 2, 1, 1),
(352, 66, 6, 'download_doc', 25, 1, 1, 2, 2),
(354, 360, 2, 'theme_set', 1, 1, 2, 2, 2),
(355, 360, 2, 'display_set', 1, 1, 2, 2, 2),
(411, 321, 5, 'unset_process', 1, 2, 2, 1, 1),
(359, 321, 2, 'recover_area', 65, 2, 2, 1, 1),
(360, 321, 2, 'set_area', 70, 2, 2, 1, 1),
(410, 321, 5, 'set_process', 1, 2, 2, 2, 1),
(363, 236, 2, 'feed_list', 60, 1, 1, 1, 1),
(364, 321, 2, 'feed_edit', 1, 1, 2, 1, 1),
(409, 321, 5, 'select_process', 1, 2, 2, 1, 1),
(367, 321, 2, 'profile_edit', 1, 1, 2, 1, 2),
(405, 321, 5, 'login_set_process', 1, 2, 2, 2, 1),
(369, 66, 6, 'trailer_doc', 20, 1, 1, 2, 2),
(373, 360, 2, 'load_set', 1, 1, 2, 2, 2),
(374, 360, 2, 'config_set', 1, 2, 2, 2, 2),
(404, 321, 5, 'ignore_process', 1, 2, 2, 1, 1),
(403, 321, 5, 'edit_process', 1, 2, 2, 1, 1),
(378, 359, 2, 'feed_recover', 1, 2, 2, 1, 2),
(380, 174, 14, 'top_report', 3, 1, 2, 1, 2),
(381, 174, 14, 'config_report', 111, 1, 2, 2, 2),
(382, 60, 2, 'metail_list', 20, 1, 1, 1, 1),
(383, 321, 2, 'metail_edit', 1, 1, 2, 1, 1),
(385, 321, 2, 'user_view', 1, 2, 2, 1, 1),
(386, 321, 19, 'feed_atom', 1, 2, 2, 2, 2),
(390, 174, 14, 'search_report', 5, 1, 2, 1, 2),
(377, 321, 27, 'autocontact_autouser_ajax', 1, 2, 2, 1, 2),
(414, 321, 11, 'login_unset_process', 1, 1, 2, 2, 2),
(400, 321, 29, 'offer_editor', 1, 2, 2, 1, 1),
(401, 321, 29, 'transfer_editor', 1, 2, 2, 1, 1),
(429, 321, 2, 'channel_edit', 1, 1, 2, 1, 1),
(430, 441, 2, 'channel_list', 60, 1, 1, 1, 1),
(431, 441, 2, 'membership_list', 80, 1, 2, 1, 1),
(432, 321, 2, 'membership_edit', 1, 2, 2, 1, 1),
(433, 321, 2, 'renewal_edit', 100, 2, 2, 1, 1),
(434, 441, 2, 'renewal_list', 70, 1, 1, 1, 1),
(435, 321, 2, 'cost_edit', 103, 1, 2, 1, 1),
(436, 441, 2, 'cost_list', 85, 2, 2, 1, 1),
(437, 441, 2, 'transaction_list', 75, 1, 1, 1, 1),
(438, 321, 2, 'transaction_edit', 1, 2, 2, 1, 1),
(439, 321, 2, 'cycle_edit', 95, 2, 2, 1, 1),
(440, 441, 2, 'cycle_list', 65, 1, 1, 1, 1),
(441, 68, 3, 'member_area', 90, 2, 2, 1, 2),
(442, 321, 2, 'renewage_edit', 101, 2, 2, 1, 1),
(443, 441, 2, 'renewage_list', 71, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_phase`
--

CREATE TABLE IF NOT EXISTS `ts_phase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ts_phase`
--

INSERT INTO `ts_phase` (`id`, `name`) VALUES
(1, 'phase_open'),
(2, 'phase_closed'),
(3, 'phase_neutral');

-- --------------------------------------------------------

--
-- Table structure for table `ts_point`
--

CREATE TABLE IF NOT EXISTS `ts_point` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ts_point`
--

INSERT INTO `ts_point` (`id`, `name`) VALUES
(1, 'start'),
(2, 'continue'),
(3, 'end'),
(4, 'nextend');

-- --------------------------------------------------------

--
-- Table structure for table `ts_pubkey`
--

CREATE TABLE IF NOT EXISTS `ts_pubkey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `value` varbinary(32768) NOT NULL,
  `modified` datetime NOT NULL,
  `default` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ts_pubkey`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_range`
--

CREATE TABLE IF NOT EXISTS `ts_range` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `ts_range`
--

INSERT INTO `ts_range` (`id`, `name`, `value`) VALUES
(1, 'range_005_mile', 5),
(2, 'range_010_mile', 10),
(3, 'range_025_mile', 25),
(4, 'range_050_mile', 50),
(5, 'range_100_mile', 100),
(6, 'range_000_mile', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ts_rating`
--

-- CREATE TABLE IF NOT EXISTS `ts_rating` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `cycle_id` int(11) NOT NULL,
--   `source_user_id` int(11) NOT NULL,
--   `destination_user_id` int(11) NOT NULL,
--   `channel_id` int(11) NOT NULL,
--   `grade_id` int(11) NOT NULL,
--   `team_id` int(11) NOT NULL,
--   `description` varchar(255) NOT NULL DEFAULT '',
--   `modified` datetime NOT NULL,
--   `active` tinyint(1) NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `modified` (`modified`),
--   KEY `source_user_id` (`source_user_id`),
--   KEY `destination_user_id` (`destination_user_id`),
--   KEY `grade_id` (`grade_id`),
--   KEY `team_id` (`team_id`),
--   KEY `channel_id` (`channel_id`),
--   KEY `cycle_id` (`cycle_id`)
-- ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=218 ;

--
-- Dumping data for table `ts_rating`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_renewage`
--

CREATE TABLE IF NOT EXISTS `ts_renewage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `point_id` int(11) NOT NULL,
  `renewal_id` int(11) NOT NULL,
  `timeframe_id` tinyint(4) NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `point_id` (`point_id`,`renewal_id`,`timeframe_id`,`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ts_renewage`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_renewal`
--

CREATE TABLE IF NOT EXISTS `ts_renewal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cycle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating_value` double NOT NULL,
  `value` double NOT NULL,
  `start` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `cycle_id` (`cycle_id`),
  KEY `start` (`start`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `ts_renewal`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_server`
--

CREATE TABLE IF NOT EXISTS `ts_server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ts_server`
--

INSERT INTO `ts_server` (`id`, `user_id`, `name`, `modified`, `active`) VALUES
(1, 132, 'localhost', '2012-02-22 12:24:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_status`
--

CREATE TABLE IF NOT EXISTS `ts_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ts_status`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_tag`
--

CREATE TABLE IF NOT EXISTS `ts_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `remote_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent_id` (`parent_id`,`name`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2849 ;

--
-- Dumping data for table `ts_tag`
--

INSERT INTO `ts_tag` (`id`, `server_id`, `remote_id`, `parent_id`, `user_id`, `name`, `modified`, `active`) VALUES
(1, 1, 1, 1, 132, '<|!|>', '2009-10-14 17:42:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_team`
--

CREATE TABLE IF NOT EXISTS `ts_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `modified` (`modified`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=199 ;

--
-- Dumping data for table `ts_team`
--

INSERT INTO `ts_team` (`id`, `user_id`, `name`, `description`, `modified`, `active`) VALUES
(175, 111, '<|root|>', '<|root|>', '2011-07-21 11:20:22', 1),
(103, 111, '<|*|>', '<|*|>', '2009-08-15 14:37:46', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_theme`
--

CREATE TABLE IF NOT EXISTS `ts_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `order` tinyint(4) NOT NULL,
  `random` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `ts_theme`
--

INSERT INTO `ts_theme` (`id`, `name`, `order`, `random`, `active`) VALUES
(1, 'theme_red', 10, 1, 1),
(2, 'theme_orange', 20, 1, 1),
(3, 'theme_yellow', 30, 1, 1),
(4, 'theme_green', 40, 1, 1),
(5, 'theme_blue', 50, 1, 1),
(6, 'theme_purple', 60, 1, 1),
(7, 'theme_pink', 70, 1, 1),
(8, 'theme_black', 80, 1, 1),
(9, 'theme_brown', 90, 1, 1),
(10, 'theme_gray', 100, 1, 1),
(11, 'theme_select_none', 110, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ts_timeframe`
--

CREATE TABLE IF NOT EXISTS `ts_timeframe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `ts_timeframe`
--

INSERT INTO `ts_timeframe` (`id`, `name`) VALUES
(1, 'past'),
(2, 'present'),
(3, 'future');

-- --------------------------------------------------------

--
-- Table structure for table `ts_transaction`
--

CREATE TABLE IF NOT EXISTS `ts_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `class_name_id` int(11) NOT NULL,
  `value` double NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`class_id`,`class_name_id`,`modified`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `ts_transaction`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_transfer`
--

CREATE TABLE IF NOT EXISTS `ts_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_user_id` int(11) NOT NULL,
  `destination_user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `modified` (`modified`),
  KEY `source_user_id` (`source_user_id`),
  KEY `destination_user_id` (`destination_user_id`),
  KEY `team_id` (`team_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1506 ;

--
-- Dumping data for table `ts_transfer`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_translation`
--

CREATE TABLE IF NOT EXISTS `ts_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `remote_id` int(11) NOT NULL,
  `kind_id` int(11) NOT NULL,
  `kind_name_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dialect_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '2',
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`),
  KEY `kind_id` (`kind_id`,`kind_name_id`),
  KEY `remote_id` (`remote_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3346 ;

--
-- Dumping data for table `ts_translation`
--

INSERT INTO `ts_translation` (`id`, `server_id`, `remote_id`, `kind_id`, `kind_name_id`, `user_id`, `dialect_id`, `name`, `description`, `modified`, `default`, `active`) VALUES
(58, 1, 58, 14, 151, 132, 2, 'Keyword', '', '2008-04-01 14:50:30', 1, 1),
(60, 1, 60, 14, 141, 132, 2, 'Dialect', '', '2008-07-20 02:00:14', 1, 1),
(61, 1, 30, 7, 30, 132, 2, 'About', 'MUST READ! Important for understanding our goals!', '2012-04-30 17:25:44', 1, 1),
(70, 1, 70, 10, 1, 132, 2, 'Available', '', '2008-06-07 01:54:54', 1, 1),
(71, 1, 2, 10, 2, 132, 2, 'Listed', 'Previously Neutral', '2013-09-12 19:22:51', 1, 1),
(75, 1, 75, 14, 174, 132, 2, 'Go Back', '', '2008-07-10 20:02:20', 1, 1),
(76, 1, 76, 7, 174, 132, 2, 'New', '', '2008-07-19 18:02:18', 2, 1),
(77, 1, 77, 7, 60, 132, 2, 'Home', '', '2008-07-19 18:02:33', 2, 1),
(78, 1, 78, 7, 61, 132, 2, 'Message Area', '', '2011-10-14 19:56:32', 1, 1),
(79, 1, 64, 7, 64, 132, 2, 'People', 'Communities. Teams. Locations. Users. Relationships.', '2012-04-30 17:09:02', 1, 1),
(80, 1, 62, 7, 62, 132, 2, 'Control', 'Regulate the quality of content. Manipulate value of goods/services.', '2012-04-30 17:09:28', 1, 1),
(81, 1, 81, 7, 236, 132, 2, 'Contact', '', '2008-07-19 18:04:25', 2, 1),
(82, 1, 82, 7, 66, 132, 2, 'Doc', '', '2008-07-19 18:05:09', 2, 1),
(83, 1, 83, 14, 233, 132, 2, 'Add Item', '', '2011-07-11 13:33:02', 1, 1),
(84, 1, 84, 14, 231, 132, 2, 'Login', '', '2008-07-19 18:06:43', 1, 1),
(85, 1, 85, 14, 297, 132, 2, 'Lock', '', '2008-07-19 18:06:58', 1, 1),
(86, 1, 86, 14, 250, 132, 2, 'Search', '', '2008-07-19 18:08:11', 1, 1),
(87, 1, 87, 14, 299, 132, 2, 'LoFi', '', '2008-07-19 18:15:15', 1, 1),
(88, 1, 88, 14, 300, 132, 2, 'HiFi', '', '2008-07-19 18:15:32', 1, 1),
(89, 1, 280, 7, 280, 132, 2, 'Stats', 'Recent Activity and New stuff. Shows what is new since your last login!', '2012-04-30 16:44:26', 1, 1),
(90, 1, 90, 7, 253, 132, 2, 'Singularity', '', '2008-07-20 01:40:17', 1, 1),
(91, 1, 176, 7, 176, 132, 2, 'Tag', 'Manage universal descriptors for item entries.', '2012-04-30 17:27:58', 1, 1),
(92, 1, 18, 7, 18, 132, 2, 'Category', 'Bookmark tags to help organize goods/service placement', '2012-04-30 17:29:07', 1, 1),
(93, 1, 93, 7, 292, 132, 2, 'Categorymind', '', '2009-12-10 15:08:38', 1, 1),
(94, 1, 153, 7, 153, 132, 2, 'Minder', 'Remembered things for drop down menus.', '2012-04-30 17:28:42', 1, 1),
(95, 1, 250, 7, 250, 132, 2, 'Dialect', 'Create a new language option!', '2012-04-30 17:29:32', 1, 1),
(96, 1, 275, 7, 275, 132, 2, 'Adaptation', 'perform translations on (parenthesized) items.', '2012-04-30 17:29:58', 1, 1),
(97, 1, 240, 7, 240, 132, 2, 'Item', 'Classified listings.', '2012-04-30 17:14:04', 1, 1),
(98, 1, 218, 7, 218, 132, 2, 'Visit', 'See user visit datetimes.', '2012-04-30 17:15:53', 1, 1),
(99, 1, 11, 7, 11, 132, 2, 'News', 'Group messages. Subscribe to news feeds to be continuously updated with group messages.', '2012-04-30 17:32:53', 1, 1),
(100, 1, 100, 14, 69, 132, 2, 'Success', '', '2008-07-20 01:43:26', 1, 1),
(101, 1, 103, 7, 103, 132, 2, 'Incident', 'Found a bug? Have some suggestions? Make an incident.', '2012-04-30 17:18:36', 1, 1),
(102, 1, 131, 7, 131, 132, 2, 'Feedback', 'Comment on specific incidents.', '2012-04-30 17:19:04', 1, 1),
(103, 1, 13, 7, 13, 132, 2, 'Message', 'Inbox and Outbox. Communication center!', '2012-04-30 17:19:35', 1, 1),
(104, 1, 246, 7, 246, 132, 2, 'Rating', 'View user merit. Rate users.', '2012-04-30 17:16:18', 1, 1),
(105, 1, 255, 7, 255, 132, 2, 'X-fer', 'Transaction log of your goods/services.', '2012-04-30 17:20:20', 1, 1),
(106, 1, 25, 7, 25, 132, 2, 'Team', 'Public communities that you can be associated with.', '2012-04-30 17:23:24', 1, 1),
(107, 1, 26, 7, 26, 132, 2, 'Teammate', 'Team members!', '2012-04-30 17:23:10', 1, 1),
(108, 1, 217, 7, 217, 132, 2, 'Location', 'Create a certain location.', '2012-04-30 17:23:51', 1, 1),
(109, 1, 109, 7, 291, 132, 2, 'Locationmind', '', '2009-12-10 15:08:56', 1, 1),
(110, 1, 24, 7, 24, 132, 2, 'User', 'See other users and their corresponding locations and descriptions.', '2012-04-30 17:24:24', 1, 1),
(111, 1, 22, 7, 22, 132, 2, 'Contact', 'Store your contacts and info!', '2012-04-30 17:20:55', 1, 1),
(112, 1, 112, 14, 129, 132, 2, 'Mailbox', '', '2008-07-20 01:46:53', 1, 1),
(167, 1, 167, 14, 345, 132, 2, 'Invite', '', '2008-09-22 22:38:25', 1, 1),
(114, 1, 235, 7, 235, 132, 2, 'Note', 'Notes about people including emails, websites, skype names, etc.', '2012-04-30 17:21:20', 1, 1),
(115, 1, 125, 7, 125, 132, 2, 'Group', 'Put your contacts together so you can search withing just them.', '2012-04-30 17:21:53', 1, 1),
(116, 1, 135, 7, 135, 132, 2, 'Groupmate', 'Contacts in the same group!', '2012-04-30 17:22:16', 1, 1),
(117, 1, 31, 7, 31, 132, 2, 'FAQ', 'Frequently Asked Questions.', '2012-04-30 17:26:15', 1, 1),
(118, 1, 32, 7, 32, 132, 2, 'Sitemap', 'This page.', '2012-04-30 17:26:47', 1, 1),
(119, 1, 119, 7, 33, 132, 2, 'Tutorial', '', '2008-07-20 01:49:20', 1, 1),
(120, 1, 34, 7, 34, 132, 2, 'Legal', 'You agree to these terms by using TS.', '2013-10-27 08:52:28', 1, 1),
(121, 1, 35, 7, 35, 132, 2, 'Donate', 'Want to help out TS?', '2012-04-30 17:27:33', 1, 1),
(122, 1, 122, 7, 306, 132, 2, 'Merit', '', '2008-07-20 01:50:03', 1, 1),
(123, 1, 123, 14, 175, 132, 2, 'Element', '', '2008-07-20 01:53:26', 1, 1),
(124, 1, 124, 14, 276, 132, 2, 'User L', '', '2011-08-28 11:57:16', 1, 1),
(125, 1, 125, 14, 277, 132, 2, 'Contact L', '', '2011-08-28 11:57:44', 1, 1),
(126, 1, 126, 14, 278, 132, 2, 'Group L', '', '2011-08-28 11:58:09', 1, 1),
(127, 1, 127, 14, 279, 132, 2, 'Team L', '', '2011-08-28 11:58:28', 1, 1),
(128, 1, 128, 14, 280, 132, 2, 'Location L', '', '2011-08-28 11:58:53', 1, 1),
(129, 1, 129, 14, 281, 132, 2, 'Range L', '', '2011-08-28 11:59:46', 1, 1),
(130, 1, 130, 14, 213, 132, 2, 'Order', '', '2008-07-20 01:55:43', 1, 1),
(131, 1, 131, 14, 179, 132, 2, 'Edit', '', '2008-07-20 01:56:06', 1, 1),
(132, 1, 132, 14, 178, 132, 2, 'Add', '', '2008-07-20 01:56:16', 1, 1),
(133, 1, 133, 14, 301, 132, 2, 'Transaction Complete!', '', '2011-08-17 13:00:00', 1, 1),
(134, 1, 134, 14, 134, 132, 2, 'Category', '', '2008-07-20 01:57:37', 1, 1),
(135, 1, 135, 14, 166, 132, 2, 'Thing', '', '2008-07-20 01:58:08', 1, 1),
(136, 1, 136, 14, 199, 132, 2, 'Singularity', '', '2008-07-20 01:59:06', 1, 1),
(137, 1, 137, 14, 150, 132, 2, 'Status', '', '2008-07-20 02:01:59', 1, 1),
(138, 1, 138, 14, 148, 132, 2, 'Incident', '', '2008-07-20 02:03:08', 1, 1),
(139, 1, 139, 14, 257, 132, 2, 'Phase', '', '2008-07-20 02:03:19', 1, 1),
(140, 1, 140, 14, 286, 132, 2, 'Direction', '', '2008-07-20 02:03:58', 1, 1),
(141, 1, 141, 14, 198, 132, 2, 'Grade', '', '2008-07-20 02:04:17', 1, 1),
(142, 1, 142, 14, 164, 132, 2, 'Team', '', '2008-07-20 02:05:03', 1, 1),
(143, 1, 143, 14, 159, 132, 2, 'Right', '', '2008-07-20 02:05:24', 1, 1),
(144, 1, 144, 14, 154, 132, 2, 'Location', '', '2008-07-20 02:06:00', 1, 1),
(145, 1, 145, 14, 146, 132, 2, 'Group', '', '2008-07-20 02:07:26', 1, 1),
(146, 1, 146, 14, 324, 132, 2, 'First', '', '2008-07-20 02:45:53', 1, 1),
(147, 1, 147, 14, 325, 132, 2, 'Previous', '', '2008-07-20 02:46:12', 1, 1),
(148, 1, 148, 14, 326, 132, 2, 'Next', '', '2008-07-20 02:46:24', 1, 1),
(149, 1, 149, 14, 327, 132, 2, 'Last', '', '2008-07-20 02:46:35', 1, 1),
(150, 1, 150, 14, 328, 132, 2, 'Add More', '', '2008-07-20 02:46:46', 1, 1),
(151, 1, 307, 7, 307, 132, 2, 'Worthiness', 'Topics for analyzing different systems of merit.', '2012-04-30 17:30:54', 1, 1),
(152, 1, 308, 7, 308, 132, 2, 'Virtue', 'Posts on merit related topics.', '2012-04-30 17:31:20', 1, 1),
(153, 1, 153, 14, 310, 132, 2, 'Merit Type', '', '2008-07-20 02:47:46', 1, 1),
(154, 1, 154, 14, 329, 132, 2, 'Result', '', '2008-07-20 02:49:46', 1, 1),
(155, 1, 155, 14, 176, 132, 2, 'Adaptation', '', '2011-10-14 21:09:23', 1, 1),
(156, 1, 156, 14, 330, 132, 2, 'Most Recent', '', '2008-07-20 02:51:39', 1, 1),
(157, 1, 157, 14, 311, 132, 2, 'Merit Topic', '', '2008-07-20 02:54:11', 1, 1),
(158, 1, 158, 14, 331, 132, 2, 'Advanced', '', '2009-12-06 00:00:33', 1, 1),
(159, 1, 159, 14, 332, 132, 2, 'Simple', '', '2009-12-06 00:00:18', 1, 1),
(160, 1, 160, 14, 312, 132, 2, 'Add Merit Topic', '', '2008-07-25 16:13:47', 1, 1),
(161, 1, 161, 14, 313, 132, 2, 'Add Merit Post', '', '2008-07-25 16:14:16', 1, 1),
(162, 1, 162, 14, 333, 132, 2, '?', '', '2008-08-06 02:49:56', 1, 1),
(163, 1, 163, 7, 93, 132, 2, 'Item Edit', '', '2011-07-11 12:46:19', 1, 1),
(164, 1, 164, 7, 276, 132, 2, 'Adaptation Edit', '', '2011-10-14 21:08:40', 1, 1),
(165, 1, 165, 7, 71, 132, 2, 'Incident Edit', '', '2008-08-28 00:25:12', 1, 1),
(166, 1, 321, 7, 321, 132, 2, 'Home', 'Portal for most important areas of TS. Edit your profile. Set display preferences.', '2012-04-30 16:40:49', 1, 1),
(168, 1, 329, 7, 329, 132, 2, 'Invited', 'See who invited who.', '2012-04-30 17:24:41', 1, 1),
(169, 1, 169, 7, 187, 135, 2, 'Lock Set', '', '2008-09-27 16:38:33', 1, 1),
(170, 1, 170, 7, 214, 132, 2, 'User Edit', '', '2008-09-27 17:45:41', 1, 1),
(171, 1, 171, 7, 75, 133, 2, 'Contact Edit', '', '2008-09-28 21:40:19', 1, 1),
(172, 1, 172, 14, 251, 132, 2, 'Logout', '', '2008-10-01 13:37:33', 1, 1),
(173, 1, 173, 14, 359, 132, 2, 'RSS Recover', '', '2008-10-01 13:39:11', 1, 1),
(174, 1, 174, 14, 334, 132, 2, 'My Profile', '', '2009-11-19 11:57:39', 1, 1),
(175, 1, 175, 7, 233, 132, 2, 'Note Edit', '', '2008-10-01 23:43:32', 1, 1),
(176, 1, 176, 7, 220, 132, 2, 'Enter', '', '2010-07-18 11:28:57', 1, 1),
(177, 1, 177, 7, 70, 132, 2, 'Message Send', '', '2012-03-15 15:23:19', 1, 1),
(178, 1, 178, 7, 216, 132, 2, 'Location Edit', '', '2008-10-04 01:21:11', 1, 1),
(179, 1, 179, 14, 67, 132, 2, 'Success Edit', '', '2008-10-04 01:21:49', 1, 1),
(180, 1, 180, 7, 72, 132, 2, 'News Edit', '', '2008-10-04 01:22:11', 1, 1),
(181, 1, 181, 7, 132, 132, 2, 'Feedback Edit', '', '2008-10-04 01:27:32', 1, 1),
(182, 1, 182, 7, 78, 132, 2, 'Rating Edit', '', '2008-10-04 01:28:21', 1, 1),
(183, 1, 183, 7, 309, 132, 2, 'Worthiness Edit', '', '2011-10-16 15:04:10', 1, 1),
(184, 1, 184, 7, 310, 132, 2, 'Virtue Edit', '', '2011-10-16 15:04:37', 1, 1),
(185, 1, 185, 7, 76, 132, 2, 'Team Edit', '', '2008-10-04 01:31:16', 1, 1),
(186, 1, 186, 7, 74, 132, 2, 'Teammate Edit', '', '2008-10-04 01:31:29', 1, 1),
(187, 1, 187, 7, 126, 132, 2, 'Group Edit', '', '2008-10-04 01:33:03', 1, 1),
(188, 1, 188, 7, 136, 132, 2, 'Groupmate Edit', '', '2008-10-04 01:33:28', 1, 1),
(189, 1, 189, 7, 325, 132, 2, 'Invite a Friend!', '', '2008-12-05 20:49:53', 1, 1),
(190, 1, 190, 7, 177, 132, 2, 'Tag Edit', '', '2008-10-04 01:34:44', 1, 1),
(191, 1, 191, 7, 73, 132, 2, 'Category Edit', '', '2008-10-04 01:35:01', 1, 1),
(192, 1, 192, 7, 151, 132, 2, 'Minder Edit', '', '2008-10-04 01:35:59', 1, 1),
(193, 1, 193, 7, 248, 132, 2, 'Dialect Edit', '', '2008-10-04 01:36:12', 1, 1),
(194, 1, 194, 7, 278, 132, 2, 'Dialect Set', '', '2008-10-04 12:47:02', 1, 1),
(195, 1, 195, 7, 335, 132, 2, 'Lock Unset*', '', '2008-10-05 13:14:06', 1, 1),
(196, 1, 196, 7, 414, 132, 2, 'Exit', '', '2010-07-18 11:29:24', 1, 1),
(197, 1, 197, 14, 201, 132, 2, 'Enter', '', '2008-10-30 22:07:20', 1, 1),
(198, 1, 198, 7, 340, 132, 2, 'X-fer Send', '', '2012-04-21 11:39:26', 1, 1),
(199, 1, 199, 7, 322, 132, 2, 'Mark All as Read', '', '2008-11-19 00:56:04', 1, 1),
(200, 1, 200, 14, 378, 132, 2, 'None', '', '2008-11-19 00:57:42', 1, 1),
(201, 1, 201, 14, 379, 132, 2, 'Delete', '', '2008-11-19 00:59:30', 1, 1),
(202, 1, 202, 14, 380, 132, 2, 'Import', '', '2008-11-19 00:59:50', 1, 1),
(203, 1, 203, 14, 381, 132, 2, 'Export', '', '2008-11-19 01:00:29', 1, 1),
(204, 1, 204, 14, 200, 132, 2, 'Description/Links', '', '2008-11-19 01:01:07', 1, 1),
(205, 1, 205, 14, 170, 132, 2, 'User', '', '2011-07-11 14:13:45', 1, 1),
(206, 1, 206, 14, 157, 132, 2, 'Description/Links', '', '2008-11-19 01:01:56', 1, 1),
(207, 1, 207, 14, 375, 132, 2, 'Description/Links', '', '2008-11-19 01:02:27', 1, 1),
(208, 1, 208, 7, 289, 132, 2, 'Selection Action', '', '2008-11-19 01:04:22', 1, 1),
(209, 1, 209, 14, 171, 132, 2, 'Password', '', '2008-12-11 12:49:28', 1, 1),
(210, 1, 210, 14, 342, 132, 2, 'Remember Login', '', '2008-12-11 12:49:51', 1, 1),
(211, 1, 211, 7, 316, 132, 2, 'Login Recover', '', '2009-06-11 15:22:45', 1, 1),
(212, 1, 212, 14, 389, 132, 2, 'Friendship', '', '2009-01-07 21:06:33', 1, 1),
(213, 1, 213, 14, 390, 132, 2, 'Friendship Edit', '', '2009-01-07 21:06:54', 1, 1),
(214, 1, 214, 14, 411, 132, 2, 'Qualify', '', '2009-01-07 21:16:13', 1, 1),
(215, 1, 215, 14, 392, 132, 2, 'Require', '', '2009-01-07 21:16:49', 1, 1),
(216, 1, 216, 14, 417, 132, 2, 'No results for the specified search.', '', '2009-01-20 19:43:47', 1, 1),
(217, 1, 217, 14, 418, 132, 2, 'Click HERE to browse ALL listings!', '', '2009-01-20 19:42:52', 1, 1),
(218, 1, 218, 14, 382, 132, 2, 'Your Friend''s Email', '', '2009-02-18 22:45:11', 1, 1),
(219, 1, 219, 14, 399, 132, 2, 'Friend|Aquaintance|Public', '', '2009-03-07 12:36:09', 1, 1),
(220, 1, 220, 14, 400, 132, 2, 'Friend|Aquaintance', '', '2009-03-07 12:39:18', 1, 1),
(221, 1, 221, 14, 401, 132, 2, 'Friend', '', '2009-03-07 12:39:44', 1, 1),
(222, 1, 222, 14, 402, 132, 2, 'Author', '', '2009-03-07 12:40:23', 1, 1),
(223, 1, 352, 7, 352, 132, 2, 'Download', 'Download the TS starter. Wallpapers.', '2012-04-30 17:25:25', 1, 1),
(224, 1, 351, 7, 351, 132, 2, 'Other', 'Miscellaneous pages. Analyze Merit.', '2012-04-30 17:09:52', 1, 1),
(225, 1, 225, 7, 353, 132, 2, 'Profile', '', '2009-04-19 13:17:16', 1, 1),
(226, 1, 226, 8, 1, 132, 2, 'Open', '', '2009-04-30 19:42:37', 1, 1),
(227, 1, 227, 14, 208, 132, 2, 'Recover Login', '', '2009-06-11 15:23:34', 1, 1),
(228, 1, 228, 7, 355, 132, 2, 'Display Set', '', '2009-07-02 14:39:20', 1, 1),
(229, 1, 229, 7, 354, 132, 2, 'Theme Set', '', '2009-07-02 14:39:33', 1, 1),
(230, 1, 230, 14, 172, 132, 2, 'Password Again', '', '2009-07-04 15:05:17', 1, 1),
(231, 1, 231, 14, 366, 132, 2, 'Message Notification', '', '2011-10-14 19:52:57', 1, 1),
(232, 1, 232, 14, 367, 132, 2, 'Rating Notification', '', '2009-07-04 15:06:54', 1, 1),
(233, 1, 233, 14, 368, 132, 2, 'X-fer Notification', '', '2012-04-21 11:39:45', 1, 1),
(234, 1, 234, 14, 484, 132, 2, 'Friendship Notification', '', '2009-07-04 15:09:09', 1, 1),
(235, 1, 235, 14, 505, 132, 2, 'Welcome! Please check out the FAQ at: http://www.tradeandshare.com/faq_doc/ -John', '', '2009-07-27 23:40:16', 1, 1),
(236, 1, 236, 14, 506, 132, 2, 'Team R', '', '2011-08-28 12:00:16', 1, 1),
(237, 1, 237, 14, 508, 132, 2, 'Landing Map', '', '2009-11-23 17:49:38', 1, 1),
(238, 1, 238, 14, 509, 132, 2, 'Notation', '', '2009-11-23 17:49:58', 1, 1),
(239, 1, 239, 14, 376, 132, 2, 'Advanced Mode', '', '2009-11-24 13:21:17', 1, 1),
(240, 1, 363, 7, 363, 132, 2, 'Feed', 'Manage your feeds. Potentially saved searches.', '2012-08-12 18:39:53', 1, 1),
(242, 1, 242, 14, 549, 132, 2, 'Subject', '', '2011-10-15 11:56:39', 1, 1),
(241, 1, 241, 7, 366, 132, 2, 'Teammind', '', '2009-12-10 15:05:18', 1, 1),
(243, 1, 243, 8, 2, 132, 2, 'Closed', '', '2010-05-17 00:15:49', 1, 1),
(244, 1, 244, 7, 367, 132, 2, 'Profile Edit', '', '2010-05-23 02:23:52', 1, 1),
(245, 1, 369, 7, 369, 132, 2, 'Trailer', 'Video intro.', '2012-04-30 17:25:03', 1, 1),
(246, 1, 246, 14, 137, 132, 2, 'Contact', '', '2011-07-11 14:11:37', 1, 1),
(255, 1, 255, 7, 387, 132, 2, 'Categorization', '', '2011-07-21 12:00:46', 1, 1),
(247, 1, 247, 14, 558, 132, 2, 'User Name', '', '2011-07-11 14:13:19', 1, 1),
(248, 1, 248, 14, 572, 132, 2, 'User Password', '', '2010-07-22 11:46:29', 1, 1),
(249, 1, 249, 14, 579, 132, 2, 'TS List', '', '2010-12-06 01:46:56', 1, 1),
(250, 1, 380, 7, 380, 132, 2, 'Mixed', 'All the recent info you care about in one place!', '2012-04-30 16:43:20', 1, 1),
(251, 1, 251, 8, 3, 132, 2, 'Neutral', '', '2011-01-01 13:44:13', 1, 1),
(252, 1, 252, 7, 381, 132, 2, 'Config', '', '2011-01-30 12:32:45', 1, 1),
(253, 1, 382, 7, 382, 132, 2, 'Metail', 'Add user details so people can contact you.', '2012-04-30 17:17:31', 1, 1),
(254, 1, 254, 7, 298, 132, 2, 'Contact View', '', '2011-06-01 14:16:35', 1, 1),
(256, 1, 390, 7, 390, 132, 2, 'Browse', 'Easy clicking to predefined searches!', '2012-04-30 16:46:02', 1, 1),
(257, 1, 257, 7, 388, 132, 2, 'Categorization Edit', '', '2011-07-21 12:01:22', 1, 1),
(267, 1, 267, 1, 1, 132, 2, 'Kind', '', '2011-08-09 14:58:01', 1, 1),
(266, 1, 266, 14, 612, 132, 2, 'Item', '', '2011-08-09 14:55:08', 1, 1),
(271, 1, 271, 1, 11, 132, 2, 'Tag', '', '2011-08-10 15:31:19', 1, 1),
(273, 1, 273, 1, 13, 132, 2, 'Category', '', '2011-08-10 15:30:48', 1, 1),
(274, 1, 274, 10, 3, 144, 2, 'Wanted', 'Status wanted translation', '2011-08-10 15:42:29', 1, 1),
(280, 1, 280, 1, 14, 132, 2, 'Element', '', '2011-08-17 12:43:26', 1, 1),
(281, 1, 281, 14, 608, 132, 2, 'Kind', '', '2011-08-17 12:43:58', 1, 1),
(282, 1, 282, 14, 609, 132, 2, 'Kind Name', '', '2011-08-17 12:44:24', 1, 1),
(283, 1, 283, 14, 617, 132, 2, 'Everything', '', '2011-08-17 12:45:03', 1, 1),
(284, 1, 284, 14, 618, 132, 2, 'Expand All', '', '2011-08-17 12:50:46', 1, 1),
(285, 1, 285, 14, 341, 132, 2, 'Mark All as Read', '', '2011-08-17 12:52:04', 1, 1),
(286, 1, 286, 14, 571, 132, 2, 'More', '', '2011-08-17 12:54:40', 1, 1),
(287, 1, 287, 14, 619, 132, 2, 'Less', '', '2011-08-17 12:55:40', 1, 1),
(288, 1, 288, 14, 610, 132, 2, 'Description/Links', '', '2011-10-16 19:24:10', 1, 1),
(289, 1, 289, 14, 614, 132, 2, '!', '', '2011-08-17 13:09:28', 1, 1),
(290, 1, 290, 14, 615, 132, 2, 'Add Feed', '', '2011-08-17 13:10:18', 1, 1),
(291, 1, 291, 14, 616, 132, 2, 'Extra', '', '2011-08-17 13:10:37', 1, 1),
(292, 1, 292, 14, 551, 132, 2, 'Incident', '', '2011-08-17 20:33:14', 1, 1),
(293, 1, 293, 14, 147, 132, 2, 'Description/Links', '', '2011-10-16 19:22:21', 1, 1),
(296, 1, 296, 1, 2, 132, 2, 'Container', '', '2011-08-19 19:58:12', 1, 1),
(297, 1, 297, 1, 3, 132, 2, 'Direction', '', '2011-08-19 19:59:53', 1, 1),
(298, 1, 298, 1, 4, 132, 2, 'Display', '', '2011-08-19 20:00:43', 1, 1),
(299, 1, 299, 1, 5, 132, 2, 'Grade', '', '2011-08-19 20:02:03', 1, 1),
(300, 1, 300, 1, 6, 132, 2, 'Merit Type', '', '2011-08-19 20:02:55', 1, 1),
(301, 1, 301, 1, 7, 132, 2, 'Page', '', '2011-08-19 20:13:41', 1, 1),
(302, 1, 302, 1, 8, 132, 2, 'Phase', '', '2011-08-19 20:05:33', 1, 1),
(303, 1, 303, 1, 9, 132, 2, 'Range', '', '2011-08-19 20:06:51', 1, 1),
(304, 1, 304, 1, 10, 132, 2, 'Status', '', '2011-08-19 20:08:23', 1, 1),
(305, 1, 305, 1, 12, 132, 2, 'Theme', '', '2011-08-19 20:09:30', 1, 1),
(316, 1, 316, 14, 234, 132, 2, 'Add Incident', '', '2011-08-24 13:06:09', 1, 1),
(324, 1, 324, 14, 621, 132, 2, 'Kind UID', '', '2011-08-26 17:39:49', 1, 1),
(325, 1, 325, 14, 633, 132, 2, 'Category UID', '', '2011-08-26 17:40:51', 1, 1),
(326, 1, 326, 14, 623, 132, 2, 'Direction UID', '', '2011-08-26 17:50:25', 1, 1),
(327, 1, 327, 14, 622, 132, 2, 'Container UID', '', '2011-08-26 17:51:27', 1, 1),
(328, 1, 328, 14, 624, 132, 2, 'Display UID', '', '2011-08-26 17:52:32', 1, 1),
(329, 1, 329, 14, 634, 132, 2, 'Element UID', '', '2011-08-26 17:53:27', 1, 1),
(330, 1, 330, 14, 625, 132, 2, 'Grade UID', '', '2011-08-26 17:54:22', 1, 1),
(331, 1, 331, 14, 626, 132, 2, 'Meritype UID', '', '2011-08-26 17:56:30', 1, 1),
(332, 1, 332, 14, 627, 132, 2, 'Page UID', '', '2011-08-26 17:57:43', 1, 1),
(333, 1, 333, 14, 628, 132, 2, 'Phase UID', '', '2011-08-26 17:58:47', 1, 1),
(334, 1, 334, 14, 629, 132, 2, 'Range UID', '', '2011-08-26 18:00:52', 1, 1),
(335, 1, 335, 14, 630, 132, 2, 'Status UID', '', '2011-08-26 18:02:40', 1, 1),
(336, 1, 336, 14, 631, 132, 2, 'Tag UID', '', '2011-08-26 18:03:33', 2, 1),
(337, 1, 337, 14, 632, 132, 2, 'Theme UID', '', '2011-08-26 18:04:31', 1, 1),
(338, 1, 338, 14, 635, 132, 2, 'Item UID', '', '2011-08-27 23:25:00', 1, 1),
(339, 1, 339, 14, 183, 132, 2, 'Add Note', '', '2011-08-28 12:23:49', 1, 1),
(340, 1, 340, 14, 229, 132, 2, 'Send Message', '', '2011-10-14 19:52:28', 1, 1),
(341, 1, 341, 14, 220, 132, 2, 'Add Contact', '', '2011-08-28 12:32:09', 1, 1),
(342, 1, 342, 14, 658, 132, 2, 'Edit Again', '', '2011-08-28 14:00:46', 1, 1),
(343, 1, 343, 14, 646, 132, 2, 'Group UID', '', '2011-08-28 14:06:05', 1, 1),
(344, 1, 344, 4, 7, 132, 2, 'Default', '', '2011-08-28 16:40:49', 1, 1),
(345, 1, 345, 4, 6, 132, 2, 'Unspecified', '', '2011-08-28 16:42:03', 1, 1),
(346, 1, 346, 4, 4, 132, 2, '1024 Pixels Wide', '', '2011-08-28 16:42:58', 1, 1),
(347, 1, 347, 4, 2, 132, 2, '320 Pixels Wide', '', '2011-08-28 16:43:42', 1, 1),
(348, 1, 348, 4, 3, 132, 2, '480 Pixels Wide', '', '2011-08-28 16:44:35', 1, 1),
(349, 1, 349, 14, 487, 132, 2, 'Display', '', '2011-08-28 16:45:31', 1, 1),
(350, 1, 350, 12, 1, 132, 2, 'Red', '', '2011-08-28 16:46:27', 1, 1),
(351, 1, 351, 14, 486, 132, 2, 'Theme', '', '2011-08-28 16:46:52', 1, 1),
(352, 1, 352, 12, 2, 132, 2, 'Orange', '', '2011-08-28 16:47:15', 1, 1),
(353, 1, 353, 12, 3, 132, 2, 'Yellow', '', '2011-08-28 16:47:43', 1, 1),
(354, 1, 354, 12, 4, 132, 2, 'Green', '', '2011-08-28 16:48:05', 1, 1),
(355, 1, 355, 12, 5, 132, 2, 'Blue', '', '2011-08-28 16:48:29', 1, 1),
(356, 1, 356, 12, 6, 132, 2, 'Purple', '', '2011-08-28 16:49:01', 1, 1),
(357, 1, 357, 12, 7, 132, 2, 'Pink', '', '2011-08-28 16:49:26', 1, 1),
(358, 1, 358, 12, 9, 132, 2, 'brown', '', '2011-08-28 16:49:55', 1, 1),
(359, 1, 359, 12, 8, 132, 2, 'Black', '', '2011-08-28 16:50:15', 1, 1),
(360, 1, 360, 14, 497, 132, 2, 'Gray', '', '2011-08-28 16:51:04', 1, 1),
(361, 1, 361, 12, 10, 132, 2, 'Gray', '', '2011-08-28 16:52:08', 1, 1),
(362, 1, 362, 12, 11, 132, 2, 'Remove Theme', '', '2011-08-28 16:53:50', 1, 1),
(363, 1, 363, 14, 169, 132, 2, 'Email', '', '2011-08-28 16:56:27', 1, 1),
(364, 1, 364, 14, 384, 132, 2, 'Locking On', '', '2011-08-28 16:57:09', 1, 1),
(365, 1, 365, 14, 581, 132, 2, 'Minimal Notifications On', '', '2011-08-28 16:57:56', 1, 1),
(366, 1, 366, 14, 168, 132, 2, 'Confirm Acceptance of Usage Policy', '', '2011-08-28 16:58:46', 1, 1),
(367, 1, 367, 7, 373, 132, 2, 'Load Set', '', '2011-08-28 17:04:16', 1, 1),
(368, 1, 368, 14, 564, 132, 2, 'Load Javascript', '', '2011-08-28 17:05:54', 1, 1),
(409, 1, 409, 14, 685, 132, 2, 'Description/Links', '', '2011-10-16 19:23:30', 1, 1),
(411, 1, 411, 14, 189, 132, 2, 'Description/Links', '', '2011-10-16 19:23:13', 1, 1),
(412, 1, 412, 14, 546, 132, 2, 'Intra-Team Access', '', '2011-10-14 21:51:14', 2, 1),
(413, 1, 413, 14, 545, 132, 2, 'All User Access ', '', '2011-10-14 21:51:58', 2, 1),
(414, 1, 414, 14, 717, 132, 2, 'Mixed Access', '', '2011-10-14 21:53:00', 1, 1),
(415, 1, 415, 14, 547, 132, 2, 'Inter-User Access', '', '2011-10-14 21:53:59', 2, 1),
(416, 1, 416, 14, 548, 132, 2, 'Author Only Access', '', '2011-10-14 21:55:05', 2, 1),
(417, 1, 417, 14, 544, 132, 2, 'Public Web Access', '', '2011-10-14 21:55:49', 2, 1),
(418, 1, 418, 14, 718, 132, 2, 'Does NOT Exist', '', '2011-10-14 22:02:47', 1, 1),
(419, 1, 419, 14, 719, 132, 2, 'DOES Exist', '', '2011-10-14 22:10:32', 1, 1),
(420, 1, 420, 14, 720, 132, 2, 'Field Missing', '', '2011-10-14 22:24:51', 1, 1),
(421, 1, 421, 14, 188, 132, 2, 'Add Feedback', '', '2011-10-14 22:56:55', 1, 1),
(422, 1, 422, 14, 645, 132, 2, 'Note UID', '', '2011-10-14 23:01:38', 2, 1),
(423, 1, 423, 14, 156, 132, 2, 'News', '', '2011-10-15 11:42:49', 1, 1),
(424, 1, 424, 14, 155, 132, 2, 'Description/Links', '', '2011-10-16 19:22:58', 1, 1),
(425, 1, 425, 14, 636, 132, 2, 'News UID', '', '2011-10-15 11:44:08', 1, 1),
(426, 1, 426, 14, 644, 132, 2, 'Contact UID', '', '2011-10-15 11:44:42', 1, 1),
(427, 1, 427, 14, 637, 132, 2, 'Rating UID', '', '2011-10-15 11:46:18', 1, 1),
(428, 1, 428, 14, 638, 132, 2, 'Metail UID', '', '2011-10-15 11:46:37', 1, 1),
(429, 1, 429, 14, 640, 132, 2, 'Incident UID', '', '2011-10-15 11:48:02', 1, 1),
(430, 1, 430, 14, 641, 132, 2, 'Feedback UID', '', '2011-10-15 11:48:46', 1, 1),
(431, 1, 431, 14, 642, 132, 2, 'Offer UID', '', '2011-10-15 11:51:11', 1, 1),
(432, 1, 432, 14, 643, 132, 2, 'Transfer UID', '', '2011-10-15 11:51:38', 1, 1),
(433, 1, 433, 14, 648, 132, 2, 'Feed UID', '', '2011-10-15 11:51:59', 1, 1),
(434, 1, 434, 14, 647, 132, 2, 'Groupmate UID', '', '2011-10-15 11:52:32', 1, 1),
(435, 1, 435, 14, 639, 132, 2, 'Login UID', '', '2011-10-15 11:53:16', 1, 1),
(436, 1, 436, 14, 649, 132, 2, 'Team UID', '', '2011-10-15 11:58:17', 1, 1),
(437, 1, 437, 14, 650, 132, 2, 'Teammate UID', '', '2011-10-15 11:58:49', 2, 1),
(438, 1, 438, 14, 651, 132, 2, 'Location UID', '', '2011-10-15 11:59:51', 1, 1),
(439, 1, 439, 14, 620, 132, 2, 'Known', '', '2011-10-15 12:02:29', 1, 1),
(440, 1, 440, 14, 652, 132, 2, 'User UID', '', '2011-10-15 12:20:30', 1, 1),
(441, 1, 441, 14, 653, 132, 2, 'Invited UID', '', '2011-10-15 12:21:42', 1, 1),
(442, 1, 442, 14, 654, 132, 2, 'Minder UID', '', '2011-10-15 12:23:18', 2, 1),
(444, 1, 444, 14, 710, 132, 2, 'Category Search', '', '2011-10-16 17:02:48', 1, 1),
(445, 1, 445, 14, 707, 132, 2, 'Location Search', '', '2011-10-16 17:08:58', 1, 1),
(446, 1, 446, 1, 15, 132, 2, 'Location', '', '2011-10-16 17:09:58', 1, 1),
(447, 1, 447, 14, 691, 132, 2, 'Group Search', '', '2011-10-16 17:11:12', 1, 1),
(448, 1, 448, 14, 722, 132, 2, 'Translation Kind', '', '2011-10-16 17:11:46', 1, 1),
(449, 1, 449, 14, 723, 132, 2, 'Minder Kind', '', '2011-10-16 17:12:10', 1, 1),
(450, 1, 450, 14, 692, 132, 2, 'Item Search', '', '2011-10-16 17:12:31', 1, 1),
(451, 1, 451, 14, 693, 132, 2, 'News Search', '', '2011-10-16 17:12:52', 1, 1),
(452, 1, 452, 14, 694, 132, 2, 'Rating Search', '', '2011-10-16 17:13:15', 1, 1),
(453, 1, 453, 14, 695, 132, 2, 'Metail Search', '', '2011-10-16 17:13:48', 1, 1),
(454, 1, 454, 14, 696, 132, 2, 'Visit Search', '', '2011-10-16 17:14:21', 1, 1),
(455, 1, 455, 14, 697, 132, 2, 'Incident Search', '', '2011-10-16 17:14:40', 1, 1),
(456, 1, 456, 14, 698, 132, 2, 'Feedback Search', '', '2011-10-16 17:15:00', 1, 1),
(457, 1, 457, 14, 699, 132, 2, 'Offer Search', '', '2011-10-16 17:16:11', 1, 1),
(458, 1, 458, 14, 700, 132, 2, 'X-fer Search', '', '2012-04-21 11:40:09', 1, 1),
(459, 1, 459, 14, 701, 132, 2, 'Contact Search ', '', '2011-10-16 17:17:45', 1, 1),
(460, 1, 460, 14, 702, 132, 2, 'Note Search', '', '2011-10-16 17:18:01', 1, 1),
(461, 1, 461, 14, 703, 132, 2, 'Groupmate Search', '', '2011-10-16 17:18:34', 1, 1),
(462, 1, 462, 14, 704, 132, 2, 'Feed Search', '', '2011-10-16 17:18:56', 1, 1),
(463, 1, 463, 14, 705, 132, 2, 'Team Search', '', '2011-10-16 17:19:16', 1, 1),
(464, 1, 464, 14, 706, 132, 2, 'Teammate Search', '', '2011-10-16 17:20:16', 1, 1),
(465, 1, 465, 14, 708, 132, 2, 'Invited Search', '', '2011-10-16 17:21:06', 1, 1),
(466, 1, 466, 14, 709, 132, 2, 'User Search', '', '2011-10-16 17:21:22', 1, 1),
(467, 1, 467, 14, 711, 132, 2, 'Tag Search', '', '2011-10-16 17:22:07', 1, 1),
(468, 1, 468, 14, 712, 132, 2, 'Minder Search', '', '2011-10-16 17:22:23', 1, 1),
(469, 1, 469, 14, 713, 132, 2, 'Adaptation Search', '', '2011-10-16 17:23:01', 1, 1),
(470, 1, 470, 14, 714, 132, 2, 'Worthiness Search', '', '2011-10-16 17:23:33', 1, 1),
(471, 1, 471, 14, 724, 132, 2, 'Virtue Search', '', '2011-10-16 17:26:13', 1, 1),
(472, 1, 472, 14, 716, 132, 2, 'Visit Search', '', '2011-10-16 18:48:35', 1, 1),
(473, 1, 473, 14, 209, 132, 2, 'Description/Links', '', '2011-10-16 19:22:39', 1, 1),
(474, 1, 474, 14, 689, 132, 2, 'Send', '', '2011-10-16 19:11:52', 1, 1),
(475, 1, 475, 14, 687, 132, 2, 'Email Sent', '', '2011-10-16 19:12:08', 1, 1),
(476, 1, 476, 14, 690, 132, 2, 'Send More', '', '2011-10-16 19:20:31', 1, 1),
(485, 1, 485, 14, 589, 132, 2, 'Memorize', '', '2011-10-23 00:08:34', 1, 1),
(486, 1, 486, 7, 385, 132, 2, 'User View', '', '2011-10-23 00:09:51', 1, 1),
(487, 1, 487, 7, 297, 132, 2, 'Incident View', '', '2011-10-24 22:44:04', 1, 1),
(490, 1, 1, 5, 1, 132, 2, 'No Credit', '', '2014-09-27 12:54:43', 1, 1),
(491, 1, 2, 5, 2, 132, 2, 'Quarter Credit', '', '2014-09-27 12:55:01', 1, 1),
(492, 1, 3, 5, 3, 132, 2, 'Neutral', '', '2014-08-16 11:05:06', 1, 1),
(493, 1, 4, 5, 4, 132, 2, 'Triquarter Credit', '', '2014-09-27 12:55:15', 1, 1),
(494, 1, 5, 5, 5, 132, 2, 'Full Credit', '', '2014-09-27 12:54:06', 1, 1),
(499, 1, 499, 1, 17, 132, 2, 'Boolean', '', '2011-11-01 16:25:30', 1, 1),
(500, 1, 500, 17, 2, 132, 2, 'False', 'false boolean', '2011-11-01 16:26:12', 1, 1),
(501, 1, 501, 17, 1, 132, 2, 'True', 'true boolean', '2011-11-01 16:26:35', 1, 1),
(502, 1, 502, 14, 686, 132, 2, 'Collapse All', '', '2011-11-01 16:27:52', 1, 1),
(506, 1, 506, 14, 614, 132, 2, 'Unset', '', '2011-11-01 16:46:15', 1, 1),
(507, 1, 507, 7, 364, 132, 2, 'Feed Edit', '', '2011-11-01 16:53:04', 1, 1),
(508, 1, 508, 14, 725, 132, 2, 'Default', '', '2011-11-01 16:53:34', 1, 1),
(509, 1, 509, 14, 688, 132, 2, 'Set Again', '', '2011-11-01 16:55:19', 1, 1),
(510, 1, 510, 14, 655, 132, 2, 'Translation UID', '', '2011-11-01 18:05:48', 2, 1),
(511, 1, 511, 14, 727, 132, 2, 'Error', '', '2011-11-01 18:40:15', 1, 1),
(515, 1, 515, 14, 586, 132, 2, 'Description/Links', '', '2011-11-03 22:38:30', 1, 1),
(516, 1, 516, 7, 383, 132, 2, 'Metail Edit', '', '2011-11-03 22:39:56', 1, 1),
(517, 1, 517, 14, 158, 132, 2, 'Description/Links', '', '2011-11-03 23:12:50', 1, 1),
(539, 1, 539, 7, 391, 132, 2, 'Jargon', '', '2011-11-13 19:51:05', 1, 1),
(540, 1, 540, 7, 392, 132, 2, 'Jargon Edit', '', '2011-11-13 19:51:29', 1, 1),
(541, 1, 541, 14, 729, 132, 2, 'Adapt', '', '2011-11-13 19:52:26', 1, 1),
(542, 1, 542, 14, 728, 132, 2, 'Jargon UID', '', '2011-11-13 20:30:02', 1, 1),
(543, 1, 543, 14, 730, 132, 2, 'Jargon Search', '', '2011-11-13 20:32:04', 1, 1),
(730, 1, 730, 1, 16, 132, 2, 'Team', '', '2012-03-05 12:39:40', 1, 1),
(850, 1, 416, 7, 416, 132, 2, 'My Stuff', 'manager page', '2012-04-24 19:22:41', 1, 1),
(851, 1, 733, 14, 733, 132, 2, 'Search Mixed', '', '2012-04-24 19:41:46', 1, 1),
(852, 1, 735, 14, 735, 132, 2, 'Remember', '', '2012-04-24 19:48:10', 1, 1),
(853, 1, 736, 14, 736, 132, 2, 'Forget', '', '2012-04-24 19:48:37', 1, 1),
(854, 1, 721, 14, 721, 132, 2, 'Submit', '', '2012-04-24 19:52:13', 1, 1),
(855, 1, 737, 14, 737, 132, 2, 'Default', '', '2012-04-24 19:52:37', 1, 1),
(856, 1, 569, 14, 569, 132, 2, 'Feed', '', '2012-04-24 19:56:34', 1, 1),
(857, 1, 517, 14, 517, 132, 2, 'Page', '', '2012-04-24 19:56:54', 1, 1),
(858, 1, 577, 14, 577, 132, 2, 'Query', '', '2012-04-24 19:57:24', 1, 1),
(859, 1, 739, 14, 739, 132, 2, 'Parent Tag', '', '2012-04-24 21:17:57', 2, 1),
(860, 1, 645, 14, 645, 132, 2, 'Note UID', '', '2012-04-24 22:04:19', 1, 1),
(861, 1, 613, 14, 613, 132, 2, 'Tag', '', '2012-04-24 22:07:35', 1, 1),
(862, 1, 631, 14, 631, 132, 2, 'Tag UID', '', '2012-04-24 22:09:28', 1, 1),
(864, 1, 734, 14, 734, 132, 2, 'View', '', '2012-04-24 22:16:56', 1, 1),
(865, 1, 285, 14, 285, 132, 2, 'From', '', '2012-04-24 23:10:27', 1, 1),
(866, 1, 284, 14, 284, 132, 2, 'To', '', '2012-04-24 23:11:02', 1, 1),
(867, 1, 731, 14, 731, 132, 2, 'Background Theme', '', '2012-04-25 17:21:58', 1, 1),
(868, 1, 732, 14, 732, 132, 2, 'Launcher Theme', '', '2012-04-25 17:22:22', 1, 1),
(869, 1, 726, 14, 726, 132, 2, 'Empty Result', '', '2012-04-25 18:23:31', 1, 1),
(873, 1, 740, 14, 740, 132, 2, 'Accept Friend', '', '2012-04-26 12:58:15', 1, 1),
(874, 1, 650, 14, 650, 132, 2, 'Teammate UID', '', '2012-04-26 12:58:49', 1, 1),
(875, 1, 654, 14, 654, 132, 2, 'Minder UID', '', '2012-04-26 13:07:38', 1, 1),
(878, 1, 545, 14, 545, 132, 2, 'All-User Access', '', '2012-04-26 13:56:00', 1, 1),
(879, 1, 547, 14, 547, 132, 2, 'Inter-User Access', '', '2012-04-26 13:55:42', 1, 1),
(880, 1, 548, 14, 548, 132, 2, 'Author-User Access', '', '2012-04-26 13:57:47', 1, 1),
(882, 1, 544, 14, 544, 132, 2, 'Public-Web Access', '', '2012-04-26 14:01:52', 1, 1),
(883, 1, 742, 14, 742, 132, 2, 'Intra-User Access', '', '2012-04-26 14:04:11', 1, 1),
(884, 1, 546, 14, 546, 132, 2, 'Intra-Team Access', '', '2012-04-26 14:06:01', 1, 1),
(885, 1, 655, 14, 655, 132, 2, 'Adaptation UID', '', '2012-04-26 14:16:48', 1, 1),
(886, 1, 738, 14, 738, 132, 2, 'Tag  Path', '', '2012-04-26 14:17:14', 1, 1),
(887, 1, 739, 14, 739, 132, 2, 'Parent Tag Path', '', '2012-04-26 14:18:04', 1, 1),
(888, 1, 656, 14, 656, 132, 2, 'Meritopic UID', '', '2012-04-26 14:22:57', 1, 1),
(889, 1, 3, 6, 3, 132, 2, 'Monetary', '', '2012-04-26 14:24:21', 1, 1),
(890, 1, 4, 6, 4, 132, 2, 'Identity', '', '2012-04-26 14:24:51', 1, 1),
(891, 1, 1, 6, 1, 132, 2, 'Good', '', '2012-04-26 14:25:15', 1, 1),
(892, 1, 2, 6, 2, 132, 2, 'Bad', '', '2012-04-26 14:25:39', 1, 1),
(893, 1, 657, 14, 657, 132, 2, 'Meripost UID', '', '2012-04-26 14:26:52', 1, 1),
(894, 1, 743, 14, 743, 132, 2, 'Redirect', '', '2012-04-26 16:35:23', 1, 1),
(900, 1, 417, 7, 417, 132, 2, 'TS', 'Landing page! The BIG add. The BIG search.', '2012-04-30 16:37:05', 1, 1),
(901, 1, 96, 14, 96, 132, 2, 'New', '', '2012-04-30 16:41:58', 1, 1),
(902, 1, 174, 7, 174, 132, 2, 'New', 'Check for recent activity. Conglomerations of important info.', '2012-04-30 16:49:53', 1, 1),
(903, 1, 60, 7, 60, 132, 2, 'Home', 'See public listings. Help TS grow and improve!', '2012-04-30 16:51:28', 1, 1),
(904, 1, 110, 14, 110, 132, 2, 'Contact', '', '2012-04-30 16:53:51', 1, 1),
(905, 1, 236, 7, 236, 132, 2, 'Contact', 'Personal information and messages. Private groupings of users. Feeds.', '2012-04-30 16:54:08', 1, 1),
(906, 1, 66, 7, 66, 132, 2, 'Docs', 'Learn things. Get media.', '2012-04-30 16:55:11', 1, 1),
(909, 1, 303, 7, 303, 132, 2, 'Team View', '', '2012-05-01 13:13:30', 1, 1),
(913, 1, 207, 14, 207, 132, 2, 'Add User', '', '2012-05-02 21:02:07', 1, 1),
(916, 1, 744, 14, 744, 132, 2, 'New Form', '', '2012-05-02 21:24:36', 1, 1),
(923, 1, 361, 14, 361, 132, 2, 'Recover', '', '2012-05-07 00:52:16', 1, 1),
(941, 1, 745, 14, 745, 132, 2, 'Add Login', '', '2012-05-12 14:17:55', 1, 1),
(3021, 1, 302, 7, 302, 132, 2, 'Group View', '', '2013-10-04 18:14:53', 1, 1),
(3025, 1, 419, 7, 419, 132, 2, 'Vote', '', '2013-10-10 00:26:32', 1, 1),
(3026, 1, 418, 7, 418, 132, 2, 'Vote Edit', '', '2013-10-10 00:26:55', 1, 1),
(3027, 1, 747, 14, 747, 132, 2, 'Decision', '', '2013-10-10 00:52:58', 1, 1),
(3028, 1, 18, 1, 18, 132, 2, 'Decision', '', '2013-10-10 00:53:37', 1, 1),
(3029, 1, 3, 18, 3, 132, 2, 'Disapprove', '', '2013-10-10 00:57:42', 1, 1),
(3030, 1, 2, 18, 2, 132, 2, 'Neutral', '', '2013-10-10 00:58:14', 1, 1),
(3031, 1, 746, 14, 746, 132, 2, 'Vote Search', '', '2013-10-10 00:58:42', 1, 1),
(3033, 1, 1, 18, 1, 132, 2, 'Approve', '', '2013-10-10 01:00:34', 1, 1),
(3034, 1, 748, 14, 748, 132, 2, 'Description', '', '2013-10-10 01:02:40', 1, 1),
(3039, 1, 749, 14, 749, 132, 2, 'Judge', '', '2013-10-11 23:00:41', 1, 1),
(3040, 1, 750, 14, 750, 132, 2, 'Vote UID', '', '2013-10-11 23:04:58', 1, 1),
(3043, 1, 422, 7, 422, 132, 2, 'Pager', '', '2013-10-12 16:48:58', 1, 1),
(3044, 1, 421, 7, 421, 132, 2, 'Peopler', '', '2013-10-12 16:49:23', 1, 1),
(3057, 1, 754, 14, 754, 132, 2, 'Go First', '', '2013-10-21 13:44:40', 1, 1),
(3058, 1, 752, 14, 752, 132, 2, 'Go Next', '', '2013-10-21 13:44:57', 1, 1),
(3059, 1, 753, 14, 753, 132, 2, 'Go Previous', '', '2013-10-21 13:45:21', 1, 1),
(3060, 1, 751, 14, 751, 132, 2, 'Go Back', '', '2013-10-21 13:46:03', 1, 1),
(3061, 1, 343, 14, 343, 132, 2, 'Inviting User', '', '2013-10-23 10:53:21', 1, 1),
(3062, 1, 344, 14, 344, 132, 2, 'Invite Password', '', '2013-10-23 10:53:56', 1, 1),
(3154, 1, 756, 14, 756, 132, 2, 'List', '', '2014-03-02 07:41:22', 1, 1),
(3169, 1, 426, 7, 426, 132, 2, 'ID', '', '2014-03-29 15:09:42', 1, 1),
(3170, 1, 427, 7, 427, 132, 2, 'Scanner', '', '2014-04-13 09:29:53', 1, 1),
(3220, 1, 430, 7, 430, 132, 2, 'Channel', '', '2014-06-11 10:33:41', 1, 1),
(3221, 1, 429, 7, 429, 132, 2, 'Channel Edit', '', '2014-06-11 10:34:06', 1, 1),
(3231, 1, 19, 1, 19, 132, 2, 'Channel', '', '2014-07-06 10:51:03', 1, 1),
(3293, 1, 441, 7, 441, 132, 2, 'Member', 'membership links', '2014-10-13 12:15:26', 1, 1),
(3294, 1, 437, 7, 437, 132, 2, 'Transaction', 'log of payments', '2014-10-13 12:17:03', 1, 1),
(3295, 1, 434, 7, 434, 132, 2, 'Renewal', 'log of memberships to be used in recalculation', '2014-10-13 12:16:42', 1, 1),
(3296, 1, 440, 7, 440, 132, 2, 'Cycle', 'log of membership cycles', '2014-10-13 12:17:39', 1, 1),
(3344, 1, 443, 7, 443, 132, 2, 'Renewage', '', '2015-01-04 10:29:51', 1, 1),
(3345, 1, 442, 7, 442, 132, 2, 'Renewage', '', '2015-01-04 10:30:31', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_user`
--

CREATE TABLE IF NOT EXISTS `ts_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `location_id` (`location_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=210 ;

--
-- Dumping data for table `ts_user`
--

INSERT INTO `ts_user` (`id`, `location_id`, `name`, `password`, `email`, `modified`, `active`) VALUES
(111, 1, '|root|', '98547af88af3d3aff2e1c10e430fd428', 'root@localhost', '2011-07-21 11:20:22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `ts_user_more`
--

CREATE TABLE IF NOT EXISTS `ts_user_more` (
  `id` int(11) NOT NULL,
  `notify_offer_received` tinyint(1) NOT NULL,
  `notify_teammate_received` tinyint(1) NOT NULL,
  `feature_lock` tinyint(1) NOT NULL,
  `feature_minnotify` tinyint(1) NOT NULL,
  UNIQUE KEY `user_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Column Names Must Correspond With Element Names.';

--
-- Dumping data for table `ts_user_more`
--

INSERT INTO `ts_user_more` (`id`, `notify_offer_received`, `notify_teammate_received`, `feature_lock`, `feature_minnotify`) VALUES
(111, 1, 1, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `ts_visit`
--

CREATE TABLE IF NOT EXISTS `ts_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `when` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `when` (`when`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49772 ;

--
-- Dumping data for table `ts_visit`
--


-- --------------------------------------------------------

--
-- Table structure for table `ts_vote`
--

-- CREATE TABLE IF NOT EXISTS `ts_vote` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `user_id` int(11) NOT NULL,
--   `tag_id` int(11) NOT NULL,
--   `decision_id` int(11) NOT NULL,
--   `team_id` int(11) NOT NULL,
--   `description` varchar(255) NOT NULL DEFAULT '',
--   `modified` datetime NOT NULL,
--   `active` tinyint(1) NOT NULL,
--   PRIMARY KEY (`id`),
--   KEY `tag_id` (`tag_id`),
--   KEY `user_id` (`user_id`),
--   KEY `modified` (`modified`),
--   KEY `team_id` (`team_id`)
-- ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ts_vote`
--

ALTER TABLE `ts_cycle` ADD `point_id` TINYINT NOT NULL AFTER `channel_id` , ADD INDEX ( `point_id` ) ;

-- hide renewal page
update ts_page set monitor=2, launch=2 where id=434;


-- bringing user_report into more of a reality
update ts_page set launch = 1 where id=29;

INSERT INTO `ts_element` ( `id` , `name`) VALUES ( NULL , 'channel_uid'), ( NULL , 'cycle_uid'), ( NULL , 'renewal_uid'), ( NULL , 'renewage_uid'), ( NULL , 'transaction_uid');


INSERT INTO `ts_kind` (`id`, `name`, `translation`, `minder`) VALUES (NULL, 'timeframe', '1', '2'), (NULL, 'point', '1', '2');

INSERT INTO `ts_element` (`id`, `name`) VALUES (NULL, 'find_transaction'), (NULL, 'find_cycle'), (NULL, 'find_channel'), (NULL, 'find_renewage'), (NULL, 'find_renewal');

alter table ts_renewal drop value;
alter table ts_renewal drop rating_value;

INSERT INTO `ts_element` (`id`, `name`) VALUES (NULL, 'channel_name'), (NULL, 'timeframe_name'), (NULL, 'point_name');
INSERT INTO `ts_element` (`id`, `name`) VALUES (NULL, 'channel_offset'), (NULL, 'channel_value'), (NULL, 'channel_description');


ALTER TABLE `ts_channel` ADD `percent` INT NOT NULL AFTER `value`;



-- liking --

CREATE TABLE IF NOT EXISTS `ts_score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kind_id` int(11) NOT NULL,
  `kind_name_id` int(11) NOT NULL,
  `source_user_id` int(11) NOT NULL,
  `destination_user_id` int(11) NOT NULL,
  `mark_id` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  KEY `kind_id` (`kind_id`),
  KEY `kind_name_id` (`kind_name_id`),
  KEY `source_user_id` (`source_user_id`),
  KEY `destination_user_id` (`destination_user_id`),
  KEY `mark_id` (`mark_id`),
  KEY `modified` (`modified`),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ts_mark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `ts_mark` (`id`, `name`, `value`) VALUES
(1, 'mark_like', 0),
(2, 'mark_dislike', 1);

CREATE TABLE IF NOT EXISTS `ts_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kind_id` int(11) NOT NULL,
  `kind_name_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `kind_id` (`kind_id`,`kind_name_id`),
  KEY `user_id` (`user_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `ts_carry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cycle_id` int(11) NOT NULL,
  `source_user_id` int(11) NOT NULL,
  `destination_user_id` int(11) NOT NULL,
  `score_value` double NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `source_user_id` (`source_user_id`),
  KEY `destination_user_id` (`destination_user_id`),
  KEY `modified` (`modified`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



INSERT INTO `ts_page` (
`parent_id`,
`file_id`,
`name`,
`order`,
`launch`,
`monitor`,
`login`,
`advanced`
)
VALUES
( 351, 2, 'score_list', 60, 1, 1, 1, 1),
( 351, 2, 'comment_list', 60, 1, 1, 1, 1),
( 351, 2, 'carry_list', 60, 1, 1, 1, 1)
;

insert into ts_kind set name = 'score', translation=1, minder=2;

alter table ts_carry drop description;
alter table ts_carry add value double after destination_user_id;

-- missed translations --

insert into ts_element set name = 'carry_uid';
insert into ts_element set name = 'comment_uid';
insert into ts_element set name = 'score_uid';

insert into ts_element set name = 'find_carry';
insert into ts_element set name = 'find_comment';
insert into ts_element set name = 'find_score';

insert into ts_element set name = 'like';
insert into ts_element set name = 'dislike';


alter table ts_kind add `score` tinyint(1);
update ts_kind set score = 2;

update ts_kind set score = 1 where name = 'tag';
update ts_kind set score = 1 where name = 'location';
update ts_kind set score = 1 where name = 'team';
update ts_kind set score = 1 where name = 'channel';
update ts_kind set score = 1 where name = 'score';

insert into ts_kind set name = 'news';
insert into ts_kind set name = 'offer';
insert into ts_kind set name = 'category';
insert into ts_kind set name = 'contact';
insert into ts_kind set name = 'user';
insert into ts_kind set name = 'teammate';
insert into ts_kind set name = 'incident';
insert into ts_kind set name = 'group';
insert into ts_kind set name = 'feedback';
insert into ts_kind set name = 'groupmate';
insert into ts_kind set name = 'minder';
insert into ts_kind set name = 'login';
insert into ts_kind set name = 'note';
insert into ts_kind set name = 'item';
insert into ts_kind set name = 'rating';
insert into ts_kind set name = 'dialect';
insert into ts_kind set name = 'transfer';
insert into ts_kind set name = 'translation';
insert into ts_kind set name = 'vote';
insert into ts_kind set name = 'jargon';
insert into ts_kind set name = 'meripost';
insert into ts_kind set name = 'meritopic';
insert into ts_kind set name = 'invited';
insert into ts_kind set name = 'feed';
insert into ts_kind set name = 'metail';
insert into ts_kind set name = 'membership';
insert into ts_kind set name = 'renewal';
insert into ts_kind set name = 'cost';
insert into ts_kind set name = 'transaction';
insert into ts_kind set name = 'cycle';
insert into ts_kind set name = 'renewage';
insert into ts_kind set name = 'comment';
insert into ts_kind set name = 'carry';

update ts_kind set translation = 2, minder = 2, `score` = 1 where `score` is null;

insert into ts_element set name = 'comment';

-- score report --
insert into ts_page set parent_id=321, file_id=14, name='score_report', `order`=1, launch=1, monitor=2, login=1, advanced=2;

-- like/dislike were swapped --
update ts_mark set value = 1;
update ts_mark set value = 0 where id = 2;

-- allowing dislikes to have weight --
update ts_mark set value = -1 where id = 2;

-- moving toward score instead of ratings --
delete from ts_page where name like 'user_report';
delete from ts_page where name like 'rating_list';
delete from ts_page where name like 'rating_edit';
-- commented out the ts_rating table from here as drop table may make people lose data

update ts_page set parent_id = 64 where name like 'score_list';
update ts_page set parent_id = 64, `order` = 65 where name like 'comment_list';
update ts_page set `order` = 50 where name like 'invited_list';

delete from ts_page where name like 'vote_list';
delete from ts_page where name like 'vote_edit';

delete from ts_element where name like 'decision_name';
delete from ts_element where name like 'find_vote';
delete from ts_element where name like 'vote_description';
delete from ts_element where name like 'judge';
delete from ts_element where name like 'vote_uid';

delete from ts_element where name like 'rating_add_edit';
delete from ts_element where name like 'rating_add_edit_process';
delete from ts_element where name like 'rating_list';
delete from ts_element where name like 'rating_select';
delete from ts_element where name like 'rating_select_process';
delete from ts_element where name like 'rating_description';
delete from ts_element where name like 'add_rating';
delete from ts_element where name like 'select_rating';
delete from ts_element where name like 'notify_rating_received';
delete from ts_element where name like 'rating_id';
delete from ts_element where name like 'rating_uid';
delete from ts_element where name like 'find_rating';

update ts_translation set name = 'Payout' where id = 3434;   

update ts_translation set name = 'Connect' where id = 905;

-- removing groups --
delete from ts_page where name like 'group_list';
delete from ts_page where name like 'group_edit';
delete from ts_page where name like 'selection_group';
delete from ts_page where name like 'groupmate_list';
delete from ts_page where name like 'groupmate_edit';
delete from ts_page where name like 'group_view';

delete from ts_element where name like 'groupmate_add_edit';
delete from ts_element where name like 'groupmate_add_edit_process';
delete from ts_element where name like 'groupmate_list';
delete from ts_element where name like 'group_add_edit';
delete from ts_element where name like 'group_add_edit_process';
delete from ts_element where name like 'group_list';
delete from ts_element where name like 'group_select';
delete from ts_element where name like 'group_select_process';
delete from ts_element where name like 'group_description';
delete from ts_element where name like 'group_name';
delete from ts_element where name like 'select_group';
delete from ts_element where name like 'add_groupmate';
delete from ts_element where name like 'add_group';
delete from ts_element where name like 'lock_group_name';
delete from ts_element where name like 'group_view';
delete from ts_element where name like 'group_id';
delete from ts_element where name like 'groupmate_id';
delete from ts_element where name like 'group_uid';
delete from ts_element where name like 'groupmate_uid';
delete from ts_element where name like 'find_group';
delete from ts_element where name like 'find_groupmate';


update ts_page set `order` = 40 where name = 'doc_area';
update ts_page set `order` = 35 where name = 'people_area';

-- element 
insert into ts_element set name="channel_percent";

-- another page --
insert into ts_page set parent_id = 321, file_id = 14, name = 'member_report', `order`=1, launch=1, monitor=2,login=1,advanced=2;

insert into ts_element set name = 'digest';
update ts_page  set launch = 2 where `name` = 'score_report';

ALTER TABLE `ts_gauge_renewal` CHANGE `rating_value` `score_value` DOUBLE NOT NULL; 

ALTER TABLE `ts_renewal` ADD `point_id` TINYINT NOT NULL AFTER `user_id` , ADD INDEX ( `point_id` ) ;
ALTER TABLE `ts_renewal` ADD `timeframe_id` TINYINT NOT NULL AFTER `point_id` , ADD INDEX ( `timeframe_id` ) ;
ALTER TABLE `ts_renewal` ADD `modified` datetime NOT NULL AFTER `start` , ADD INDEX ( `modified` ) ;

delete from ts_page where name like 'renewage%';
update ts_page set launch = 1 where name like 'renew%';

update ts_page set monitor = 1 where name like 'renewal_list';

insert into ts_element set name='channel_parent_id';

-- accomodate alternate scoring page
insert into  ts_page set  parent_id = 321,  file_id = 14,  name = 'cycle_report',  `order` = 2,  launch = 2,  monitor = 2,  login = 1,  advanced = 2 ;

-- no membership_list/edit and carry_list
delete from ts_page where id in (431,432,446);

-- cleanup
-- these tables can be removed but renaming is safer
RENAME TABLE `ts_renewage` TO `rm_ts_renewage` ;
RENAME TABLE `ts_grade` TO `rm_ts_grade` ;
RENAME TABLE `ts_gauge_renewal` TO `rm_ts_gauge_renewal` ;
RENAME TABLE `ts_membership` TO `rm_ts_membership` ;
RENAME TABLE `ts_carry` TO `rm_ts_carry` ;
RENAME TABLE `ts_rating` TO `rm_ts_rating` ;

RENAME TABLE `ts_vote` TO `rm_ts_vote` ;


alter table ts_decision rename rm_decision;
alter table ts_group rename rm_group;
alter table ts_link_contact_group rename rm_index_group_user;

-- accounting --
alter table ts_transaction rename ts_accounting;
alter table ts_cost rename rm_cost;
alter table ts_outcome rename rm_outcome;
update ts_page set name = 'accounting_list' where name = 'transaction_list';
update ts_page set name = 'accounting_edit' where name = 'transaction_edit';

update ts_class set name = 'renewal' where id=3;
update ts_class set name = 'cycle' where id = 2;
update ts_class set name = 'bitcoin' where id = 4;

-- merge class/kind --
ALTER TABLE `ts_accounting` CHANGE `class_id` `kind_id` INT( 11 ) NOT NULL , CHANGE `class_name_id` `kind_name_id` INT( 11 ) NOT NULL ;
ALTER TABLE `ts_kind` ADD `accounting` TINYINT NOT NULL AFTER `name` , ADD INDEX ( `accounting` ) ;
update ts_kind set accounting = 2;
update ts_kind set accounting = 1 where name like 'cycle';
update ts_kind set accounting = 1 where name like 'renewal';
INSERT INTO `ts_kind` ( `id` , `name` , `accounting` , `translation` , `minder` , `score`) VALUES ( NULL , 'manual', '1', '1', '2', '2'); 
alter table ts_class rename rm_class;
update ts_accounting set kind_id = 56;


CREATE TABLE IF NOT EXISTS `ts_manual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


update ts_element set name = 'accounting_uid' where name like 'transaction_uid';
update ts_element set name = 'find_accounting' where name like 'find_transaction';

alter table ts_accounting modify modified timestamp;
alter table ts_manual modify modified timestamp;


-- allow for donations on a cyclical basis (like renewals)
CREATE TABLE IF NOT EXISTS `ts_sponsor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_origin_id` int(11) NOT NULL,
  `cycle_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `point_id` tinyint(4) NOT NULL,
  `timeframe_id` tinyint(4) NOT NULL,
  value double NOT NULL,
  `start` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `cycle_id` (`cycle_id`),
  KEY `start` (`start`),
  KEY `point_id` (`point_id`),
  KEY `timeframe_id` (`timeframe_id`),
  KEY `modified` (`modified`),
  KEY `channel_origin_id` (`channel_origin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

insert into ts_page set parent_id = 321, file_id=2, name='sponsor_edit', `order`=101, launch=1, monitor=2, login=1, advanced=1;
insert into ts_page set parent_id = 441, file_id=2, name='sponsor_list', `order`=71, launch=1, monitor=1, login=1, advanced=1;

delete from ts_page where name = 'score_report';


CREATE TABLE IF NOT EXISTS `ts_donate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `offset` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `channel_parent_id` (`channel_parent_id`,`user_id`,`modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE `ts_sponsor` DROP `channel_origin_id`, DROP `value`;
ALTER TABLE `ts_sponsor` DROP `user_id`;

ALTER TABLE `ts_sponsor` CHANGE `cycle_id` `donate_id` INT( 11 ) NOT NULL; 


insert into ts_page set parent_id = 321, file_id = 2, name = 'donate_edit', `order` = 100, launch = 1, monitor=2, login=1, advanced =1;
insert into ts_page set parent_id = 441, file_id = 2, name = 'donate_list', `order` = 70, launch = 1, monitor=1, login=1, advanced = 1;
update ts_page set `order` = 67 where name like 'renewal_list';
ALTER TABLE `ts_donate` ADD `timeframe_id` TINYINT NOT NULL AFTER `user_id` , ADD INDEX ( `timeframe_id` ) ;

insert into ts_kind set name='donate', accounting = 2, translation = 1, minder = 2, score = 2;
insert into ts_kind set name='sponsor', accounting = 1, translation = 1, minder = 2, score = 2;
ALTER TABLE `ts_kind` ADD UNIQUE ( `name`);

-- day_size
-- only needed if not preconverted
-- update ts_channel set offset = offset * 86400;
-- update ts_donate set offset = offset * 86400;

alter table ts_kind add parent_id int(11) after id;
insert into ts_kind set parent_id = 0, name = 'accounting_hosting', accounting = 1, translation = 2, minder= 2, score = 2;
update ts_kind set parent_id = 59 where parent_id is null;
insert into ts_kind set parent_id = 0, name = 'origin', accounting = 2, translation = 2, minder= 2, score = 2;
update ts_kind set parent_id = 60 where parent_id = 59;
insert into ts_kind set parent_id = 60, name = 'accounting_mission', accounting = 1, translation = 2, minder= 2, score = 2;
update ts_kind set parent_id = 60 where id = 59 limit 1;
alter table ts_kind add index `parent_id` (`parent_id`);


update ts_kind set name = 'cycle_hostfee' where id = '59';
update ts_kind set name = 'cycle_missionfee' where id = '61';
update ts_kind set parent_id = 52 where id in (59, 61);

-- preparation for more dynamic scripts
create table ts_script (
	id int not null auto_increment,
	name varchar(255),
	primary key (id)
);
insert into ts_script set name = 'periodic/all.php';
insert into ts_script set name = 'scheduled/membership.php';
create table ts_runner (
	id int not null auto_increment,
	script_id int,
	modified datetime,
	primary key (id)
);
alter table ts_runner add index script_id (`script_id`);

alter table ts_runner add `start` datetime after script_id;

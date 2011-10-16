-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. Oktober 2011 um 17:30
-- Server Version: 5.1.44
-- PHP-Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `fermi`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_article`
--

CREATE TABLE IF NOT EXISTS `fermi_article` (
  `article_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `author_id` int(11) unsigned NOT NULL,
  `created_at` int(10) NOT NULL,
  `modified_at` int(10) NOT NULL,
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `name` (`name`),
  KEY `author_id` (`author_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=27 ;

--
-- Daten für Tabelle `fermi_article`
--

INSERT INTO `fermi_article` (`article_id`, `category_id`, `name`, `content`, `title`, `author_id`, `created_at`, `modified_at`) VALUES
(6, NULL, 'index', '<?xml version="1.0" encoding="UTF-8"?>\r\n<article>\r\n \r\n <widgets>\r\n\r\n<widget type="Headline">\r\n <values>\r\n  <value name="headline"><![CDATA[\r\nZack ich bin sowas von ne Überschrift.\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n\r\n\r\n\r\n<widget type="Text">\r\n <values>\r\n  <value name="text"><![CDATA[\r\nLorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n\r\n\r\n<widget type="Text">\r\n <values>\r\n  <value name="text"><![CDATA[\r\nBÄMBÄMBÄMBÄMßßßßÄÄÄÖÖÖÜÜÜäääöööüüü\r\n]]></value>\r\n </values>\r\n</widget>\r\n</widgets>\r\n\r\n<areas>\r\n <area name="sidebar">\r\n  <widget type="Headline">\r\n   <values>\r\n    <value name="headline"><![CDATA[\r\n     Auf der Seite\r\n    ]]></value>\r\n    </values>\r\n   </widget>\r\n </area>\r\n</areas>\r\n\r\n\r\n</article>', 'Home', 1, 0, 0),
(12, 2, 'hans', '<?xml version="1.0" encoding="UTF-8"?>\r\n<article>\r\n<widgets>\r\n\r\n<widget type="Headline">\r\n <values>\r\n  <value name="headline"><![CDATA[\r\nHans Seite\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n<widget type="Links">\r\n <values>\r\n  <value name="title"><![CDATA[Linkliste]]></value>\r\n  <value name="linklist"><![CDATA[\r\n<a href="http://www.spyka.net" title="spyka Webmaster resources">spyka webmaster</a>,\r\n<a href="http://www.justfreetemplates.com" title="free web templates">Free web templates</a>,\r\n<a href="http://www.spyka.net/forums" title="webmaster forums">Webmaster forums</a>,\r\n<a href="http://www.awesomestyles.com/mybb-themes" title="mybb themes">MyBB themes</a>,\r\n<a href="http://www.awesomestyles.com" title="free phpbb3 themes">phpBB3 styles</a>,\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n\r\n\r\n<widget type="Text">\r\n <values>\r\n  <value name="text"><![CDATA[\r\nZwei flinke Boxer jagen die quirlige Eva und ihren Mops durch Sylt. Franz jagt im komplett verwahrlosten Taxi quer durch Bayern. Zwölf Boxkämpfer jagen Viktor quer über den großen Sylter Deich. Vogel Quax zwickt Johnys Pferd Bim. Sylvia wagt quick den Jux bei Pforzheim. Polyfon zwitschernd aßen Mäxchens Vögel Rüben, Joghurt und Quark. "Fix, Schwyz! " quäkt Jürgen blöd vom Paß. Victor jagt zwölf Boxkämpfer quer über den großen Sylter Deich. Falsches Üben von Xylophonmusik quält jeden größeren Zwerg. Heizölrückstoßabdämpfung. Zwei flinke Boxer jagen die quirlige Eva und ihren Mops durch Sylt. Franz jagt im komplett verwahrlosten Taxi quer durch Bayern. Zwölf Boxkämpfer jagen Viktor quer über den großen Sylter Deich. Vogel Quax zwickt Johnys Pferd Bim. Sylvia wagt quick den Jux bei Pforzheim. Polyfon zwitschernd aßen Mäxchens Vögel Rüben, Joghurt und Quark. "Fix, Schwyz! " quäkt Jürgen blöd vom Paß. Victor jagt zwölf Boxkämpfer quer über den großen Sylter Deich. Falsches Üben von Xylophonmusik quält jeden größeren Zwerg. Heizölrückstoßabdämpfung. Zwei flinke Boxer jagen die quirlige Eva und ihren Mops durch Sylt. Franz jagt im komplett verwahrlosten Taxi quer durch Bayern. Zwölf Boxkämpfer jagen Viktor quer über den großen Sylter Deich. Vogel Quax zwickt Johnys Pferd Bim. Sylvia wagt quick den Jux\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n</widgets>\r\n</article>', 'About', 1, 0, 0),
(25, 2, 'test', '<?xml version="1.0" encoding="UTF-8"?>\r\n<article>\r\n<widgets>\r\n\r\n<widget type="Headline">\r\n <values>\r\n  <value name="headline"><![CDATA[\r\nTest Seite\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n<widget type="Text">\r\n <values>\r\n  <value name="text"><![CDATA[\r\nÜberall dieselbe alte Leier. Das Layout ist fertig, der Text lässt auf sich warten. Damit das Layout nun nicht nackt im Raume steht und sich klein und leer vorkommt, springe ich ein: der Blindtext. Genau zu diesem Zwecke erschaffen, immer im Schatten meines großen Bruders »Lorem Ipsum«, freue ich mich jedes Mal, wenn Sie ein paar Zeilen lesen. Denn esse est percipi - Sein ist wahrgenommen werden. Und weil Sie nun schon die Güte haben, mich ein paar weitere Sätze lang zu begleiten, möchte ich diese Gelegenheit nutzen, Ihnen nicht nur als Lückenfüller zu dienen, sondern auf etwas hinzuweisen, das es ebenso verdient wahrgenommen zu werden: Webstandards nämlich. Sehen Sie, Webstandards sind das Regelwerk, auf dem Webseiten aufbauen. So gibt es Regeln für HTML, CSS, JavaScript oder auch XML; Worte, die Sie vielleicht schon einmal von Ihrem Entwickler gehört haben. Diese Standards sorgen dafür, dass alle Beteiligten aus einer Webseite den größten Nutzen ziehen. Im Gegensatz zu früheren Webseiten müssen wir zum Beispiel nicht mehr zwei verschiedene Webseiten für den Internet Explorer und einen anderen Browser programmieren. Es reicht eine Seite, die - richtig angelegt - sowohl auf verschiedenen Browsern im Netz funktioniert, aber ebenso gut für den Ausdruck oder\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n</widgets>\r\n</article>', 'Testseite', 1, 1314744579, 1314744579),
(26, 3, 'test2', '<?xml version="1.0" encoding="UTF-8"?>\r\n<article>\r\n<widgets>\r\n\r\n<widget type="Headline">\r\n <values>\r\n  <value name="headline"><![CDATA[\r\nTest Seite\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n<widget type="Text">\r\n <values>\r\n  <value name="text"><![CDATA[\r\nÜberall dieselbe alte Leier. Das Layout ist fertig, der Text lässt auf sich warten. Damit das Layout nun nicht nackt im Raume steht und sich klein und leer vorkommt, springe ich ein: der Blindtext. Genau zu diesem Zwecke erschaffen, immer im Schatten meines großen Bruders »Lorem Ipsum«, freue ich mich jedes Mal, wenn Sie ein paar Zeilen lesen. Denn esse est percipi - Sein ist wahrgenommen werden. Und weil Sie nun schon die Güte haben, mich ein paar weitere Sätze lang zu begleiten, möchte ich diese Gelegenheit nutzen, Ihnen nicht nur als Lückenfüller zu dienen, sondern auf etwas hinzuweisen, das es ebenso verdient wahrgenommen zu werden: Webstandards nämlich. Sehen Sie, Webstandards sind das Regelwerk, auf dem Webseiten aufbauen. So gibt es Regeln für HTML, CSS, JavaScript oder auch XML; Worte, die Sie vielleicht schon einmal von Ihrem Entwickler gehört haben. Diese Standards sorgen dafür, dass alle Beteiligten aus einer Webseite den größten Nutzen ziehen. Im Gegensatz zu früheren Webseiten müssen wir zum Beispiel nicht mehr zwei verschiedene Webseiten für den Internet Explorer und einen anderen Browser programmieren. Es reicht eine Seite, die - richtig angelegt - sowohl auf verschiedenen Browsern im Netz funktioniert, aber ebenso gut für den Ausdruck oder\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n</widgets>\r\n</article>', 'Testseite', 1, 1314744579, 1314744579);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_category`
--

CREATE TABLE IF NOT EXISTS `fermi_category` (
  `category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `fermi_category`
--

INSERT INTO `fermi_category` (`category_id`, `parent_id`, `name`) VALUES
(2, 3, 'Allgemein'),
(3, NULL, 'Hunde'),
(4, NULL, 'Katzen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_property`
--

CREATE TABLE IF NOT EXISTS `fermi_property` (
  `property_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`property_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `fermi_property`
--

INSERT INTO `fermi_property` (`property_id`, `name`) VALUES
(1, 'structure_open_categories'),
(2, 'zeugs');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_right`
--

CREATE TABLE IF NOT EXISTS `fermi_right` (
  `right_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`right_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `fermi_right`
--

INSERT INTO `fermi_right` (`right_id`, `path`) VALUES
(1, '*/*/*'),
(2, 'admin/dashboard/*');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_right_role`
--

CREATE TABLE IF NOT EXISTS `fermi_right_role` (
  `right_role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) unsigned DEFAULT NULL,
  `right_id` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`right_role_id`),
  UNIQUE KEY `UQ_8e56061aa67ca191693fecda1efe930d4aab7f3a` (`right_id`,`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Daten für Tabelle `fermi_right_role`
--

INSERT INTO `fermi_right_role` (`right_role_id`, `role_id`, `right_id`) VALUES
(1, 2, 1),
(11, 4, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_role`
--

CREATE TABLE IF NOT EXISTS `fermi_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `fermi_role`
--

INSERT INTO `fermi_role` (`role_id`, `name`, `parent_id`) VALUES
(1, 'default', 4),
(2, 'admin', 1),
(4, 'super', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_role_user`
--

CREATE TABLE IF NOT EXISTS `fermi_role_user` (
  `role_user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` tinyint(3) unsigned DEFAULT NULL,
  `user_id` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`role_user_id`),
  UNIQUE KEY `UQ_e25d3cdf36978e9b84d1e0e4732b23444d9575aa` (`role_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `fermi_role_user`
--

INSERT INTO `fermi_role_user` (`role_user_id`, `role_id`, `user_id`) VALUES
(4, 2, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_setting`
--

CREATE TABLE IF NOT EXISTS `fermi_setting` (
  `setting_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `fermi_setting`
--

INSERT INTO `fermi_setting` (`setting_id`, `name`, `value`) VALUES
(1, 'pagetitle', 'fermi powered site'),
(2, 'skin', 'dynamic');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_user`
--

CREATE TABLE IF NOT EXISTS `fermi_user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pass` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `fermi_user`
--

INSERT INTO `fermi_user` (`user_id`, `email`, `pass`, `salt`, `name`) VALUES
(1, 'ephimetheuss@gmail.com', 'ed28efcb2eba6009b7ede715b3f0496d68d38058', 'asd476', 'paul'),
(2, 'hans@email.de', 'admin', 'asd476', 'hans');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_user_property`
--

CREATE TABLE IF NOT EXISTS `fermi_user_property` (
  `user_property_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `property_id` int(10) unsigned NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user_property_id`),
  KEY `property_id` (`property_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `fermi_user_property`
--

INSERT INTO `fermi_user_property` (`user_property_id`, `user_id`, `property_id`, `value`) VALUES
(2, 1, 1, 'test');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fermi_widgetarea`
--

CREATE TABLE IF NOT EXISTS `fermi_widgetarea` (
  `widgetarea_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`widgetarea_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `fermi_widgetarea`
--

INSERT INTO `fermi_widgetarea` (`widgetarea_id`, `name`, `content`) VALUES
(1, 'sidebar', '<?xml version="1.0" encoding="UTF-8"?>\r\n<area>\r\n<widgets>\r\n\r\n<widget type="Links">\r\n <values>\r\n  <value name="title"><![CDATA[Linkliste]]></value>\r\n  <value name="linklist"><![CDATA[\r\n<a href="http://www.spyka.net" title="spyka Webmaster resources">spyka webmaster</a>,\r\n<a href="http://www.justfreetemplates.com" title="free web templates">Free web templates</a>,\r\n<a href="http://www.spyka.net/forums" title="webmaster forums">Webmaster forums</a>,\r\n<a href="http://www.awesomestyles.com/mybb-themes" title="mybb themes">MyBB themes</a>,\r\n<a href="http://www.awesomestyles.com" title="free phpbb3 themes">phpBB3 styles</a>,\r\n]]></value>\r\n </values>\r\n</widget>\r\n\r\n</widgets>\r\n</area>');

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `fermi_article`
--
ALTER TABLE `fermi_article`
  ADD CONSTRAINT `fermi_article_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `fermi_user` (`user_id`),
  ADD CONSTRAINT `fermi_article_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `fermi_category` (`category_id`);

--
-- Constraints der Tabelle `fermi_category`
--
ALTER TABLE `fermi_category`
  ADD CONSTRAINT `fermi_category_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `fermi_category` (`category_id`);

--
-- Constraints der Tabelle `fermi_user_property`
--
ALTER TABLE `fermi_user_property`
  ADD CONSTRAINT `fermi_user_property_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `fermi_user` (`user_id`),
  ADD CONSTRAINT `fermi_user_property_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `fermi_property` (`property_id`);

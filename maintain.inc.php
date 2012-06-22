<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

// Default config
define('default_guestbook_config', serialize(array(
  'comments_validation' => false,
  'email_admin_on_comment' => false,
  'email_admin_on_comment_validation' => false,
  'nb_comment_page' => 15,
)));

// Installation
function plugin_install()
{
  global $prefixeTable;

  pwg_query("
CREATE TABLE `" . $prefixeTable . "guestbook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `author` varchar(255) NOT NULL,
  `author_id` smallint(5) DEFAULT NULL,
  `anonymous_id` varchar(45) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `rate` float(5,2) unsigned DEFAULT NULL,
  `validated` enum('true','false') NOT NULL DEFAULT 'false',
  `validation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8
;");
  
  conf_update_param('guestbook', default_guestbook_config);
}

// Activation
function plugin_activate()
{
  global $conf;
  
  if (!isset($conf['guestbook']))
  {
    conf_update_param('guestbook', default_guestbook_config);
  }
}

// Uninstallation
function plugin_uninstall()
{
  global $prefixeTable;

  pwg_query("DROP TABLE `" . $prefixeTable . "guestbook`;");
  pwg_query("DELETE FROM " . CONFIG_TABLE . " WHERE `param` = 'guestbook';");
}
?>
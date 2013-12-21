<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class GuestBook_maintain extends PluginMaintain
{
  private $installed = false;
  
  private $default_conf = array(
    'comments_validation' => false,
    'email_admin_on_comment' => false,
    'email_admin_on_comment_validation' => true,
    'nb_comment_page' => 15,
    'activate_rating' => true,
    'guest_can_view' => true,
    'guest_can_add' => true,
    );

  function install($plugin_version, &$errors=array())
  {
    global $conf, $prefixeTable;
  
    if (empty($conf['guestbook']))
    {
      $conf['guestbook'] = serialize($this->default_conf);
      conf_update_param('guestbook', $conf['guestbook']);
    }
    else
    {
      $old_conf = is_string($conf['guestbook']) ? unserialize($conf['guestbook']) : $conf['guestbook'];
      
      if (!isset($old_conf['guest_can_view']))
      {
        $old_conf['guest_can_view'] = true;
        $old_conf['guest_can_add'] = true;
      }
      
      $conf['guestbook'] = serialize($old_conf);
      conf_update_param('guestbook', $conf['guestbook']);
    }
  
    pwg_query('
CREATE TABLE IF NOT EXISTS `' . $prefixeTable . 'guestbook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT "0000-00-00 00:00:00",
  `author` varchar(255) NOT NULL,
  `author_id` smallint(5) DEFAULT NULL,
  `anonymous_id` varchar(45) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `content` longtext NOT NULL,
  `rate` float(5,2) unsigned DEFAULT NULL,
  `validated` enum("true","false") NOT NULL DEFAULT "false",
  `validation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8
;');

    $this->installed = true;
  }

  function activate($plugin_version, &$errors=array())
  {
    if (!$this->installed)
    {
      $this->install($plugin_version, $errors);
    }
  }

  function deactivate()
  {
  }

  function uninstall()
  {
    global $prefixeTable;
  
    pwg_query('DROP TABLE `' . $prefixeTable . 'guestbook`;');

    conf_delete_param('guestbook');
  }
}

<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class GuestBook_maintain extends PluginMaintain
{
  private $table;
  
  private $default_conf = array(
    'comments_validation' => false,
    'email_admin_on_comment' => false,
    'email_admin_on_comment_validation' => true,
    'nb_comment_page' => 15,
    'activate_rating' => true,
    'guest_can_view' => true,
    'guest_can_add' => true,
    'menu_link' => true,
    );
  
  function __construct($id)
  {
    global $prefixeTable;
    
    parent::__construct($id);
    $this->table = $prefixeTable.'guestbook';
  }

  function install($plugin_version, &$errors=array())
  {
    global $conf;
  
    if (empty($conf['guestbook']))
    {
      conf_update_param('guestbook', $this->default_conf, true);
    }
    else
    {
      $old_conf = safe_unserialize($conf['guestbook']);
      
      if (!isset($old_conf['guest_can_view']))
      {
        $old_conf['guest_can_view'] = true;
        $old_conf['guest_can_add'] = true;
      }
      if (!isset($old_conf['menu_link']))
      {
        $old_conf['menu_link'] = true;
      }
      
      conf_update_param('guestbook', $old_conf, true);
    }
  
    pwg_query('
CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT now(),
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
  }
  
  function update($old_version, $new_version, &$errors=array())
  {
    $this->install($new_version, $errors);
  }

  function uninstall()
  {
    pwg_query('DROP TABLE `' . $this->table . '`;');

    conf_delete_param('guestbook');
  }
}

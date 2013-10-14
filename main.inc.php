<?php
/*
Plugin Name: GuestBook
Version: auto
Description: Add a guestbook to the gallery
Plugin URI: auto
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable;


defined('GUESTBOOK_ID') or define('GUESTBOOK_ID', basename(dirname(__FILE__)));
define('GUESTBOOK_PATH' ,   PHPWG_PLUGINS_PATH . GUESTBOOK_ID . '/');
define('GUESTBOOK_TABLE' ,  $prefixeTable . 'guestbook');
define('GUESTBOOK_ADMIN',   get_root_url().'admin.php?page=plugin-' . GUESTBOOK_ID);
define('GUESTBOOK_URL',     get_absolute_root_url() . make_index_url(array('section' => 'guestbook')));
define('GUESTBOOK_VERSION', 'auto');


include_once(GUESTBOOK_PATH . 'include/events.inc.php');

add_event_handler('init', 'guestbook_init');

// admin page
if (defined('IN_ADMIN'))
{
  add_event_handler('get_admin_plugin_menu_links', 'gb_admin_menu');
}

// menu entry
add_event_handler('blockmanager_apply', 'gb_menubar_apply', EVENT_HANDLER_PRIORITY_NEUTRAL+10);

// guestbook section
add_event_handler('loc_end_section_init', 'gb_section_init');
add_event_handler('loc_end_index', 'gb_index');

// stuff
// add_event_handler('get_stuffs_modules', 'gb_register_stuffs_module')


function guestbook_init()
{
  global $conf, $pwg_loaded_plugins;
  
  // apply upgrade if needed
  if (
    GUESTBOOK_VERSION == 'auto' or
    $pwg_loaded_plugins[GUESTBOOK_ID]['version'] == 'auto' or
    version_compare($pwg_loaded_plugins[GUESTBOOK_ID]['version'], GUESTBOOK_VERSION, '<')
  )
  {
    // call install function
    include_once(GUESTBOOK_PATH . 'include/install.inc.php');
    guestbook_install();
    
    // update plugin version in database
    if ( $pwg_loaded_plugins[GUESTBOOK_ID]['version'] != 'auto' and GUESTBOOK_VERSION != 'auto' )
    {
      $query = '
UPDATE '. PLUGINS_TABLE .'
SET version = "'. GUESTBOOK_VERSION .'"
WHERE id = "'. GUESTBOOK_ID .'"';
      pwg_query($query);
      
      $pwg_loaded_plugins[GUESTBOOK_ID]['version'] = GUESTBOOK_VERSION;
      
      if (defined('IN_ADMIN'))
      {
        $_SESSION['page_infos'][] = 'GuestBook updated to version '. GUESTBOOK_VERSION;
      }
    }
  }
  
  // load plugin language file
  load_language('plugin.lang', GUESTBOOK_PATH);
  
  // prepare plugin configuration
  $conf['guestbook'] = unserialize($conf['guestbook']);
}

<?php
/*
Plugin Name: GuestBook
Version: auto
Description: Add a guestbook to the gallery
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=609
Author: Mistic
Author URI: http://www.strangeplanet.fr
*/

if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

global $prefixeTable;


define('GUESTBOOK_PATH' , PHPWG_PLUGINS_PATH . basename(dirname(__FILE__)) . '/');
define('GUESTBOOK_TABLE' , $prefixeTable . 'guestbook');
define('GUESTBOOK_URL', make_index_url(array('section' => 'guestbook')));
define('GUESTBOOK_ADMIN', get_root_url().'admin.php?page=plugin-' . basename(dirname(__FILE__)));

add_event_handler('init', 'gb_init');

function gb_init()
{
  global $conf;
  
  load_language('plugin.lang', GUESTBOOK_PATH);
  $conf['guestbook'] = unserialize($conf['guestbook']);
  
  // menubar
  if (script_basename() != 'admin')
  {
    add_event_handler('blockmanager_apply', 'gb_menubar_apply', EVENT_HANDLER_PRIORITY_NEUTRAL+10);
  }
  else
  {
    add_event_handler('get_admin_plugin_menu_links', 'gb_admin_menu');
  }
  
  // guestbook section
  add_event_handler('loc_end_section_init', 'gb_section_init');
  add_event_handler('loc_end_index', 'gb_index');
  
  // stuff
  // add_event_handler('get_stuffs_modules', 'gb_register_stuffs_module')
}

function gb_menubar_apply($menu_ref_arr)
{
  $menu = &$menu_ref_arr[0];
  
  if ( ($block = $menu->get_block('mbMenu')) != null )
  {
    array_push($block->data, array(
      'URL' => GUESTBOOK_URL,
      'TITLE' => l10n('GuestBook'),
      'NAME' => l10n('GuestBook')
    ));
  }
}

function gb_section_init()
{
  global $tokens, $page;

  if ($tokens[0] == 'guestbook')
  {
    $page['section'] = 'guestbook';
    $page['title'] = l10n('GuestBook');
  }
}

function gb_index() 
{
  global $template, $page, $conf;

  if (isset($page['section']) and $page['section'] == 'guestbook')
  {
    include(GUESTBOOK_PATH . '/include/guestbook.inc.php');
  }
}

/*function gb_register_stuffs_module($modules)
{
  array_push($modules, array(
    'path' => GUESTBOOK_PATH . '/stuffs_module',
    'name' => GB_NAME,
    'description' => l10n('gb_stuffs_desc'),
  ));

  return $modules;
}*/

function gb_admin_menu($menu) 
{
  array_push($menu, array(
    'NAME' => 'GuestBook',
    'URL' => GUESTBOOK_ADMIN,
  ));
  return $menu;
}

?>
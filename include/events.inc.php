<?php
defined('GUESTBOOK_PATH') or die('Hacking attempt!');

function gb_admin_menu($menu) 
{
  array_push($menu, array(
    'NAME' => 'GuestBook',
    'URL' => GUESTBOOK_ADMIN,
  ));
  return $menu;
}

function gb_menubar_apply($menu_ref_arr)
{
  global $conf;
  
  if (is_a_guest() && !$conf['guestbook']['guest_can_view'])
  {
    return;
  }
  
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
  global $tokens, $page, $conf;

  if ($tokens[0] == 'guestbook')
  {
    add_event_handler('loc_begin_page_header', 'gb_page_header');
    
    $page['section'] = 'guestbook';
    $page['title'] = l10n('GuestBook');
    $page['section_title'] = '<a href="'.get_gallery_home_url().'">'.l10n('Home').'</a>'.$conf['level_separator'].l10n('GuestBook');
  }
}

function gb_page_header()
{
  global $page;
  $page['body_id'] = 'theGuestBook';
}

function gb_index() 
{
  global $template, $page, $conf;
  
  if (is_a_guest() && !$conf['guestbook']['guest_can_view'])
  {
    access_denied();
  }

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
<?php
defined('GUESTBOOK_PATH') or die('Hacking attempt!');

function gb_admin_menu($menu) 
{
  $menu[] = array(
    'NAME' => 'GuestBook',
    'URL' => GUESTBOOK_ADMIN,
  );
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
  
  if (($block = $menu->get_block('mbMenu')) != null)
  {
    $block->data['guestbook'] = array(
      'URL' => GUESTBOOK_URL,
      'TITLE' => l10n('GuestBook'),
      'NAME' => l10n('GuestBook')
    );
  }
}

function gb_section_init()
{
  global $tokens, $page, $conf;

  if ($tokens[0] == 'guestbook')
  {
    $page['section'] = 'guestbook';
    $page['body_id'] = 'theGuestBook';
    $page['is_external'] = true;
    $page['is_homepage'] = false;
    
    $page['title'] = l10n('GuestBook');
    $page['section_title'] = '<a href="'.get_gallery_home_url().'">'.l10n('Home').'</a>'.$conf['level_separator'].l10n('GuestBook');
  }
}

function gb_index() 
{
  global $template, $page, $conf;

  if (isset($page['section']) and $page['section'] == 'guestbook')
  {
    if (is_a_guest() && !$conf['guestbook']['guest_can_view'])
    {
      access_denied();
    }
  
    include(GUESTBOOK_PATH . '/include/guestbook.inc.php');
  }
}

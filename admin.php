<?php
if(!defined('GUESTBOOK_PATH')) die('Hacking attempt!');

global $template, $page;

// tabsheet
include_once(PHPWG_ROOT_PATH.'admin/include/tabsheet.class.php');
$page['tab'] = (isset($_GET['tab'])) ? $_GET['tab'] : $page['tab'] = 'pending';
  
$tabsheet = new tabsheet();
$tabsheet->add('pending', l10n('Pending Comments'), GUESTBOOK_ADMIN . '-pending');
$tabsheet->add('config', l10n('Configuration'), GUESTBOOK_ADMIN . '-config');
$tabsheet->select($page['tab']);
$tabsheet->assign();

// include page
include(GUESTBOOK_PATH . 'admin/' . $page['tab'] . '.php');

// template
$template->assign(array(
  'GUESTBOOK_PATH' => GUESTBOOK_PATH,
  'GUESTBOOK_ADMIN' => GUESTBOOK_ADMIN,
));

$template->assign_var_from_handle('ADMIN_CONTENT', 'guestbook');


?>
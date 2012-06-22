<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

if (isset($_POST['submit']))
{
  $conf['guestbook'] = array(
    'comments_validation' => isset($_POST['comments_validation']),
    'email_admin_on_comment' => isset($_POST['email_admin_on_comment']),
    'email_admin_on_comment_validation' => isset($_POST['email_admin_on_comment_validation']),
    'nb_comment_page' => $_POST['nb_comment_page'],
    );
    
  conf_update_param('guestbook', serialize($conf['guestbook']));
  array_push($page['infos'], l10n('Information data registered in database'));
}

$template->assign($conf['guestbook']);

$template->set_filename('guestbook', dirname(__FILE__).'/template/config.tpl');

?>
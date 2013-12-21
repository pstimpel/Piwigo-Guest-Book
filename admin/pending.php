<?php
if (!defined('GUESTBOOK_PATH')) die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH.'admin/include/functions.php');

// +-----------------------------------------------------------------------+
// |                                actions                                |
// +-----------------------------------------------------------------------+

if (!empty($_POST))
{
  if (empty($_POST['comments']))
  {
    $page['errors'][] =l10n('Select at least one comment');
  }
  else
  {
    include_once(GUESTBOOK_PATH .'include/functions_comment.inc.php');
    check_input_parameter('comments', $_POST, true, PATTERN_ID);
    
    if (isset($_POST['validate']))
    {
      validate_user_comment_guestbook($_POST['comments']);

      $page['infos'][] = l10n_dec(
        '%d user comment validated', '%d user comments validated',
        count($_POST['comments'])
        );
    }

    if (isset($_POST['reject']))
    {
      delete_user_comment_guestbook($_POST['comments']);

      $page['infos'][] =l10n_dec(
        '%d user comment rejected', '%d user comments rejected',
        count($_POST['comments'])
        );
    }
  }
}


// +-----------------------------------------------------------------------+
// |                           comments display                            |
// +-----------------------------------------------------------------------+
include(GUESTBOOK_PATH .'include/functions.inc.php');

$list = array();

$query = '
SELECT 
    c.id, 
    c.date, 
    c.author, 
    '.$conf['user_fields']['username'].' AS username, 
    c.content,
    c.website,
    c.email,
    c.rate
  FROM '.GUESTBOOK_TABLE.' AS c
    LEFT JOIN '.USERS_TABLE.' AS u
      ON u.'.$conf['user_fields']['id'].' = c.author_id
  WHERE validated = \'false\'
  ORDER BY c.date DESC
;';
$result = pwg_query($query);

while ($row = pwg_db_fetch_assoc($result))
{
  if (empty($row['author_id'])) 
  {
    $author_name = $row['author'];
  }
  else
  {
    $author_name = stripslashes($row['username']);
  }
  
  $template->append(
    'comments',
    array(
      'ID' => $row['id'],
      'AUTHOR' => trigger_event('render_comment_author', $author_name),
      'DATE' => format_date($row['date'], true),
      'CONTENT' => trigger_event('render_comment_content',$row['content']),
      'EMAIL' => $row['email'],
      'WEBSITE' => $row['website'],
      'WEBSITE_NAME' => preg_replace('#^(https?:\/\/)#i', null, $row['website']),
      'STARS' => get_stars($row['rate'], GUESTBOOK_PATH .'template/jquery.raty/'),
      'RATE' => $row['rate'],
      )
    );

  $list[] = $row['id'];
}


$template->assign(array(
  'LIST' => implode(',', $list),
  'F_ACTION' => GUESTBOOK_ADMIN . '-pending',
  ));

$template->set_filename('guestbook', realpath(GUESTBOOK_PATH . 'admin/template/pending.tpl'));

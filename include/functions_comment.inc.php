<?php
defined('GUESTBOOK_PATH') or die('Hacking attempt!');

include_once(PHPWG_ROOT_PATH.'include/functions_comment.inc.php');


function insert_user_comment_guestbook( &$comm, $key )
{
  global $conf, $user, $page;

  $comm = array_merge($comm,
    array(
      'ip' => $_SERVER['REMOTE_ADDR'],
      'agent' => $_SERVER['HTTP_USER_AGENT']
    )
   );
  
  if (!$conf['guestbook']['comments_validation'] or is_admin())
  {
    $comment_action='validate';
  }
  else
  {
    $comment_action='moderate';
  }

  // author
  if (!is_classic_user())
  {
    if (empty($comm['author']))
    {
      $page['errors'][] = l10n('Please enter your username');
      $comment_action='reject';
    }
    else
    {
      $comm['author_id'] = $conf['guest_id'];
      // if a guest try to use the name of an already existing user,
      // he must be rejected
      $query = '
SELECT COUNT(*) AS user_exists
  FROM '.USERS_TABLE.'
  WHERE '.$conf['user_fields']['username']." = '".addslashes($comm['author'])."'
;";
      $row = pwg_db_fetch_assoc(pwg_query($query));
      
      if ($row['user_exists'] == 1)
      {
        $page['errors'][] = l10n('This login is already used by another user');
        $comment_action='reject';
      }
    }
  }
  else
  {
    $comm['author'] = addslashes($user['username']);
    $comm['author_id'] = $user['id'];
  }

  // content
  if (empty($comm['content']))
  {
    $comment_action='reject';
  }

  // key
  if (!verify_ephemeral_key(@$key))
  {
    $comment_action='reject';
    $_POST['cr'][] = 'key';
  }
  
  // email
  if (empty($comm['email']) and is_classic_user() and !empty($user['email']))
  {
    $comm['email'] = $user['email'];
  }
  else if (empty($comm['email']) and $conf['comments_email_mandatory'])
  {
    $page['errors'][] = l10n('mail address must be like xxx@yyy.eee (example : jack@altern.org)');
    $comment_action='reject';
  }
  else if (!empty($comm['email']) and !email_check_format($comm['email']))
  {
    $page['errors'][] = l10n('mail address must be like xxx@yyy.eee (example : jack@altern.org)');
    $comment_action='reject';
  }
  
  // website
  if (!empty($comm['website']))
  {
    $comm['website'] = strip_tags($comm['website']);
    if (!preg_match('/^(https?:\/\/)/i', $comm['website']))
    {
      $comm['website'] = 'http://'.$comm['website'];
    }
    if (!url_check_format($comm['website']))
    {
      $page['errors'][] = l10n('invalid website address');
      $comment_action='reject';
    }
  }
  
  // anonymous id = ip address
  $ip_components = explode('.', $_SERVER["REMOTE_ADDR"]);
  if (count($ip_components) > 3)
  {
    array_pop($ip_components);
  }
  $comm['anonymous_id'] = implode('.', $ip_components);
  
  // comment validation and anti-spam
  if ($comment_action!='reject' and $conf['anti-flood_time']>0 and !is_admin())
  {
    $reference_date = pwg_db_get_flood_period_expression($conf['anti-flood_time']);
    
    $query = '
SELECT COUNT(1) FROM '.GUESTBOOK_TABLE.'
  WHERE 
    date > '.$reference_date.'
    AND author_id = '.$comm['author_id'];
    if (!is_classic_user())
    {
      $query.= '
      AND anonymous_id = "'.$comm['anonymous_id'].'"';
    }
    $query.= '
;';
    
    list($counter) = pwg_db_fetch_row(pwg_query($query));
    if ($counter > 0)
    {
      $page['errors'][] = l10n('Anti-flood system : please wait for a moment before trying to post another comment');
      $comment_action='reject';
    }
  }
  
  // perform more spam check
  $comment_action = trigger_change('user_comment_check', $comment_action, $comm, 'guestbook');

  if ($comment_action!='reject')
  {
    $query = '
INSERT INTO '.GUESTBOOK_TABLE.'(
    author, 
    author_id, 
    anonymous_id,
    content, 
    date, 
    validated, 
    validation_date, 
    website, 
    rate, 
    email
  )
  VALUES (
    \''.$comm['author'].'\',
    '.$comm['author_id'].',
    \''.$comm['anonymous_id'].'\',
    \''.$comm['content'].'\',
    NOW(),
    \''.($comment_action=='validate' ? 'true':'false').'\',
    '.($comment_action=='validate' ? 'NOW()':'NULL').',
    '.(!empty($comm['website']) ? '\''.$comm['website'].'\'' : 'NULL').',
    '.(!empty($comm['rate']) ? $comm['rate'] : 'NULL').',
    '.(!empty($comm['email']) ? '\''.$comm['email'].'\'' : 'NULL').'
  )
';

    pwg_query($query);

    $comm['id'] = pwg_db_insert_id(GUESTBOOK_TABLE);

    if ( ($conf['guestbook']['email_admin_on_comment'] and 'validate' == $comment_action)
        or ($conf['guestbook']['email_admin_on_comment_validation'] and 'moderate' == $comment_action))
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');

      $comment_url = add_url_params(GUESTBOOK_URL, array('comment_id'=>$comm['id']));

      $keyargs_content = array(
        get_l10n_args('Author: %s', stripslashes($comm['author']) ),
        get_l10n_args('Comment: %s', stripslashes($comm['content']) ),
        get_l10n_args('', ''),
        get_l10n_args('Manage this user comment: %s', $comment_url)
      );

      if ('moderate' == $comment_action)
      {
        $keyargs_content[] = get_l10n_args('', '');
        $keyargs_content[] = get_l10n_args('(!) This comment requires validation', '');
      }

      pwg_mail_notification_admins(
        get_l10n_args('Comment by %s', stripslashes($comm['author']) ),
        $keyargs_content
      );
    }
  }
  
  return $comment_action;
}

function update_user_comment_guestbook($comment, $post_key)
{
  global $conf;

  $comment_action = 'validate';

  if (!verify_ephemeral_key($post_key))
  {
    $comment_action='reject';
  }
  else if (!$conf['guestbook']['comments_validation'] or is_admin()) // should the updated comment must be validated
  {
    $comment_action='validate';
  }
  else
  {
    $comment_action='moderate';
  }

  if ($comment_action!='reject')
  {
    $user_where_clause = '';
    if (!is_admin())
    {
      $user_where_clause = '   AND author_id = \''.
	$GLOBALS['user']['id'].'\'';
    }

    $query = '
UPDATE '.GUESTBOOK_TABLE.'
  SET content = \''.$comment['content'].'\',
      validated = \''.($comment_action=='validate' ? 'true':'false').'\',
      validation_date = '.($comment_action=='validate' ? 'NOW()':'NULL').'
  WHERE id = '.$comment['comment_id'].
$user_where_clause.'
;';
    $result = pwg_query($query);
    
    // mail admin and ask to validate the comment
    if ($result and $conf['guestbook']['email_admin_on_comment_validation'] and 'moderate' == $comment_action) 
    {
      include_once(PHPWG_ROOT_PATH.'include/functions_mail.inc.php');
      
      $comment_url = add_url_params(GUESTBOOK_URL, array('comment_id'=>$comm['id']));

      $keyargs_content = array(
        get_l10n_args('Author: %s', stripslashes($GLOBALS['user']['username']) ),
        get_l10n_args('Comment: %s', stripslashes($comment['content']) ),
        get_l10n_args('', ''),
        get_l10n_args('Manage this user comment: %s', $comment_url),
        get_l10n_args('', ''),
        get_l10n_args('(!) This comment requires validation', ''),
      );

      pwg_mail_notification_admins(
        get_l10n_args('Comment by %s', stripslashes($GLOBALS['user']['username']) ),
        $keyargs_content
      );
    }
  }
  
  return $comment_action;
}

function get_comment_author_id_guestbook($comment_id, $die_on_error=true)
{
  $query = '
SELECT
    author_id
  FROM '.GUESTBOOK_TABLE.'
  WHERE id = '.$comment_id.'
;';
  $result = pwg_query($query);

  if (pwg_db_num_rows($result) == 0)
  {
    if ($die_on_error)
    {
      fatal_error('Unknown comment identifier');
    }
    else
    {
      return false;
    }
  }
  
  list($author_id) = pwg_db_fetch_row($result);

  return $author_id;
}

function delete_user_comment_guestbook($comment_id)
{
  $user_where_clause = '';
  if (!is_admin())
  {
    $user_where_clause = ' AND author_id = \''.$GLOBALS['user']['id'].'\'';
  }
  
  if (is_array($comment_id))
  {
    $where_clause = 'id IN('.implode(',', $comment_id).')';
  }
  else
  {
    $where_clause = 'id = '.$comment_id;
  }
    
  $query = '
DELETE FROM '.GUESTBOOK_TABLE.'
  WHERE '.$where_clause.
$user_where_clause.'
;';
  pwg_query($query);
}

function validate_user_comment_guestbook($comment_id)
{
  if (is_array($comment_id))
  {
    $where_clause = 'id IN('.implode(',', $comment_id).')';
  }
  else
  {
    $where_clause = 'id = '.$comment_id;
  }
    
  $query = '
UPDATE '.GUESTBOOK_TABLE.'
  SET validated = \'true\'
    , validation_date = NOW()
  WHERE '.$where_clause.'
;';
  pwg_query($query);
}

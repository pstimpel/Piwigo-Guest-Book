<?php
define('PHPWG_ROOT_PATH','../../../');
include_once( PHPWG_ROOT_PATH.'include/common.inc.php' );

if (!is_admin()) die('Access denied');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>GuestBook import</title>
  
  <style>
    h3 { color:red; }
  </style>
</head>

<body>
<h2>GuestBook import</h2>

<?php
$show_form = true;

if (isset($_POST['image_id']))
{
  if (!preg_match('#^[0-9]+$#', $_POST['image_id']))
  {
    echo '<h3>Incorrect image Id</h3><br>';
  }
  else
  {
    $query = '
SELECT * FROM '.COMMENTS_TABLE.'
  WHERE image_id = '.$_POST['image_id'].'
;';
    $comms = hash_from_query($query, 'id');
    
    if (!count($comms))
    {
      echo '<h3>No comments for this picture</h3><br>';
    }
    else
    {
      mass_inserts(
        GUESTBOOK_TABLE,
        array('date', 'author', 'author_id', 'anonymous_id', 'email', 'website', 'content', 'rate', 'validated', 'validation_date'),
        $comms
        );
        
      echo '<h3>'.count($comms).' comments imported into the Guestbook</h3><br>';
      $show_form = false;
    }
  }
}

if ($show_form)
{
?>
Just enter the id of your old guestbook picture (the Id can be found a the picture edition page, near the thumbnail) and click the <b>import</b> button.
<form action="" method="post">
<label>Image id. <input type="text" size="5" name="image_id"></label><br>
<input type="submit" value="import">
</form>

<?php
}
?>

<br>
<a href="<?php echo GUESTBOOK_ADMIN; ?>-config">Go back</a>

</body>
</html>
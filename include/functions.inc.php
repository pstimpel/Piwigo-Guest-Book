<?php

if (!defined('GUESTBOOK_PATH')) die('Hacking attempt!');

if (!function_exists('is_valid_email'))
{
  function is_valid_email($mail_address)
  {
    if (version_compare(PHP_VERSION, '5.2.0') >= 0)
    {
      return filter_var($mail_address, FILTER_VALIDATE_EMAIL)!==false;
    }
    else
    {
      $atom   = '[-a-z0-9!#$%&\'*+\\/=?^_`{|}~]';   // before  arobase
      $domain = '([a-z0-9]([-a-z0-9]*[a-z0-9]+)?)'; // domain name
      $regex = '/^' . $atom . '+' . '(\.' . $atom . '+)*' . '@' . '(' . $domain . '{1,63}\.)+' . $domain . '{2,63}$/i';

      if (!preg_match($regex, $mail_address)) return false;
      return true;
    }
  }
}

function is_valid_url($url)
{
  if (version_compare(PHP_VERSION, '5.2.0') >= 0)
  {
    return filter_var($url, FILTER_VALIDATE_URL)!==false;
  }
  else
  if (1)
  {
    $regex = '#^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$#i';

    if (!preg_match($regex, $url)) return false;
    return true;
  }
}

function get_stars($score, $path)
{
  if ($score === null) return null;
  
  $max = 5;
  $score = min(max($score, 0), $max);
  $floor = floor($score);
  
  $html = null;
  for ($i=1; $i<=$floor; $i++)
  {
    $html.= '<img alt="'.$i.'" src="'.$path.'star-on.png">';
  }
  
  if ($score != $max)
  {
    if ($score-$floor <= .25)
    {
      $html.= '<img alt="'.($floor+1).'" src="'.$path.'star-off.png">';
    }
    else if ($score-$floor <= .75)
    {
      $html.= '<img alt="'.($floor+1).'" src="'.$path.'star-half.png">';
    }
    else
    {
      $html.= '<img alt="'.($floor+1).'" src="'.$path.'star-on.png">';
    }
  
    for ($i=$floor+2; $i<=$max; $i++)
    {
      $html.= '<img alt="'.$i.'" src="'.$path.'star-off.png">';
    }
  }
  
  return $html;
}

?>
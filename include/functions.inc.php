<?php
defined('GUESTBOOK_PATH') or die('Hacking attempt!');

function get_stars($score, $path)
{
  if (!isset($score)) return null;
  
  $max = 5;
  $score = min(max($score, 0), $max);
  $floor = floor($score);
  
  $html = '';
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
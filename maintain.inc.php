<?php
if (!defined('PHPWG_ROOT_PATH')) die('Hacking attempt!');

defined('GUESTBOOK_ID') or define('GUESTBOOK_ID', basename(dirname(__FILE__)));
include_once(PHPWG_PLUGINS_PATH . GUESTBOOK_ID . '/include/install.inc.php');

// Installation
function plugin_install()
{
  guestbook_install();
  define('guestbook_installed', true);
}

// Activation
function plugin_activate()
{
  if (!defined('guestbook_installed'))
  {
    guestbook_install();
  }
}

// Uninstallation
function plugin_uninstall()
{
  guestbook_uninstall();
}
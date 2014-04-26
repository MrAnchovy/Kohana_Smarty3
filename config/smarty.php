<?php defined('SYSPATH') OR die('No direct access allowed.');

/* To override any of Smarty's configuration, use the same format as below in
 * application/config/smarty.php. 
 */

$modpath = realpath(dirname(__FILE__).'/..');

return array (

  // this is now set using Kohana::find_file but you can override it here
  // 'smarty_class_file' => "$modpath/vendor/smarty/libs/Smarty.class.php",
  'smarty_class_file' => NULL,

  // Don't bother to check writablity of compile directory in production mode
  'check_dirs' => !(Kohana::$environment==Kohana::PRODUCTION),

  // some smarty settings need to be dealt with separately...

  // this is perhaps more useful than the default PASSTHRU or REMOVE or ALLOW
  // but needs special handling because the constants are not defined yet
  'php_handling' => 'Smarty::PHP_QUOTE',

  // most config settings can be made in this automagically handled array
  'smarty_config' => array(

    // auto literal on delimiters with whitspace
    // new in Smarty 3 and very useful for inline CSS but may break existing templates
    'auto_literal'    => TRUE,

    // useful when developing, override to false in your application's config
    // for a small speed increase in development mode
    'compile_check'   =>  !(Kohana::$environment==Kohana::PRODUCTION),

    // useful to set TRUE when developing plugins
    'force_compile'   =>  FALSE,

    // show errors for unassigned template variables - this doesn't seem to work
    // as intended because it throws E_NOTICE if false and if it is TRUE it doesn't
    // allow the 'default' modifier to work
    'error_unassigned'=> FALSE,

    // we can use Kohana's cache directory for our compiled templates
    'compile_dir'     => Kohana::$cache_dir . '/smarty_compiled',
    // ... and the smarty cache (only used if it is enabled)
    'cache_dir'       => Kohana::$cache_dir . '/smarty_cache',

    // module 'views' directories are added when initialising, but
    // you can put additional ones (which are searched first) here. Note that this
    // is only used by Smarty's include and template inheritance functions, 
    // View::factory() uses Kohana::find_file()
    'template_dir'    =>  array(
      APPPATH.'views',
    ),

    // TODO need to create some useful Kohana plugins. Investigate whether
    // pre-registering gives a meaningful speed gain.
    'plugins_dir'     =>  array(
      APPPATH.'smarty_plugins',
      "$modpath/smarty_plugins",
    ),

    // If you want to use smarty config files, put them in this place
    'config_dir'      =>  APPPATH.'smarty_config',
    ),

);

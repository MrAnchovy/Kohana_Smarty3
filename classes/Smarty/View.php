<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Acts as an object wrapper for HTML pages with embedded PHP, called "views".
 * Variables can be assigned with the view object and referenced locally within
 * the view.
 *
 * @TODO       Refactor this to combine Smarty_View and Kohana_View implementations
 *             of methods into a single class Smarty_Kohana_View (use a property
 *             $_view_type with values NULL or Smarty_Kohana_View::TYPE_SMARTY)
 *             and choose actions accordingly. You can then easily override the
 *             class if you want.
 *
 * @package   Smarty3
 * @author    Mr Anchovy
 * @copyright (c) 2011-2014 Mr Anchovy
 * @license   http://kohanaframework.org/license
 * @version   2.0.3-dev
 */
class Smarty_View extends Kohana_View {

const VERSION = '2.0.3-dev';

// View filename
protected $_file;

// Array of local variables
protected $_data = array();

/**
 * Clone count for benchmarking
 */
protected static $_clone_count;

/**
 * Clone time for benchmarking
 */
protected $_clone_time;

/**
 * Initialisation time for benchmarking
 */
protected static $_init_time;

/**
 * Smarty object instance for current template
 */
protected $_smarty;

/**
 * Directory containing Smarty.class.php
 */
protected static $_smarty_path;

/**
 * Prototype Smarty object to be initialised once and cloned for each template
 */
protected static $_smarty_prototype;

/**
 * Clone time for benchmarking
 */
protected static $_total_clone_time;

/**
 * Sets the initial view filename and local data. Views should almost
 * always only be created using [View::factory].
 *
 *     $view = new View($file);
 *
 * @param   string  view filename
 * @param   array   array of values
 * @return  void
 * @uses    View::set_filename
 */
public function __construct($file = NULL, array $data = NULL) {
  // initialise smarty
  $this->smarty();

  if ($file !== NULL) {
    $this->set_filename($file);
  }

  if ($data !== NULL) {
    // Add the values to the current data
    $this->_smarty->assign($data);
  }
}

/**
 * Magic method, searches for the given variable and returns its value.
 * Local variables will be returned before global variables.
 *
 *     $value = $view->foo;
 *
 * [!!] If the variable has not yet been set, an exception will be thrown.
 *
 * @param   string  variable name
 * @return  mixed
 * @throws  Kohana_Exception
 */
public function & __get($key) {
  // unlike the parent, this does not check for a global variable with this name
  $var = $this->_smarty->getVariable($key);
  if ( $var instanceof Undefined_Smarty_Variable ) {
    throw new Kohana_Exception('View variable is not set: :var',
      array(':var' => $key));
  } else {
    return $var->value;
  }
}


/**
 * Magic method, determines if a variable is set.
 *
 *     isset($view->foo);
 *
 * [!!] `NULL` variables are not considered to be set by [isset](http://php.net/isset).
 *
 * @param   string  variable name
 * @return  boolean
 */
public function __isset($key) {
  return $this->_smarty->getVariable($key)->value===NULL;
  // unlike the parent, this does not check for a global variable with this name
}

/**
 * Magic method, unsets a given variable.
 *
 *     unset($view->foo);
 *
 * @param   string  variable name
 * @return  void
 */
public function __unset($key) {
  $this->_smarty->clearAssign($key);
  // unlike the parent, this does not unset a global variable with this name
  return $this;
}

/**
 * Assigns a value by reference. The benefit of binding is that values can
 * be altered without re-setting them. It is also possible to bind variables
 * before they have values. Assigned values will be available as a
 * variable within the view file:
 *
 *     // This reference can be accessed as $ref within the view
 *     $view->bind('ref', $bar);
 *
 * @param   string   variable name
 * @param   mixed    referenced variable
 * @return  $this
 */
public function bind($key, & $value) {
  $this->_smarty->assignByRef($key, $value);
  return $this;
}

/**
 * Captures the output that is generated when a view is included.
 * The view data will be extracted to make local variables. This method
 * is static to prevent object scope resolution.
 *
 *     $output = View::capture($file, $data);
 *
 * @param   string  filename
 * @param   array   variables
 * @return  string
 */
protected static function capture($kohana_view_filename, array $kohana_view_data) {
  throw new Exception('capture method not implemented in View_Smarty class');
}

/**
 *
 */
private static function create_dir($path, $name='') {
  if ( file_exists($path) ) {
    if ( is_dir($path) ) {
      throw new Kohana_Exception('Could not write to :name directory',
        array('name' => $name));
    } else {
      throw new Kohana_Exception(':name path is a file',
        array('name' => $name));
    }
  } else {
    try {
      mkdir($path);
    } catch (Exception $e) {
      throw new Kohana_Exception('Could not create :name directory',
        array('name' => $name));
    }
    if ( !is_writeable($path) ) {
      throw new Kohana_Exception('Created :name directory but could not write to it',
        array('name' => $name));
    }
  }
}

/**
 * Returns a new View object. If you do not define the "file" parameter,
 * you must call [View::set_filename].
 *
 *     $view = View::factory($file);
 *
 * @param   string  view filename
 * @param   array   array of values
 * @return  View
 */
public static function factory($file = NULL, array $data = NULL) {
  return new Smarty_View($file, $data);
}

/**
 * Returns the current smarty object, creating it and the prototype if necessary
 *
 * @return   Smarty object
 */
public function smarty() {

  // see if we need to set up the smarty object for this instance
  if ( $this->_smarty===NULL ) {
    // set time for benchmarking
    $time = microtime(TRUE);
    $this->_smarty = clone self::smarty_prototype();
    $time = microtime(TRUE) - $time;
    $this->_clone_time = $time;
    self::$_total_clone_time += $time;
    self::$_clone_count++;
  }
  return $this->_smarty;
}

/**
 * Returns the smarty prototype object, creating it if necessary
 *
 * @return   Smarty prototype object
 */
public static function smarty_prototype() {
  // set time for benchmarking
  $time = microtime(TRUE);
  // see if we need to set up the prototype smarty object
  if ( self::$_smarty_prototype===NULL ) {

    // nearly everything can be done in the config file
    if ( Kohana::VERSION > '3.2' ) {
      $config = Kohana::$config->load('smarty');
    } else {
      $config = Kohana::config('smarty');
    }

    // locate a Smarty class - first check if it is already loaded
    if ( !class_exists('Smarty', false) ) {
      // next check if a path is set in config/smarty.php
      if ( $file = $config->smarty_class_file ) {
        require_once($file);
        // save the location in case we have more than one Smarty version around
        self::$_smarty_path = realpath(dirname($file)).DIRECTORY_SEPARATOR;
      } elseif ( !class_exists('Smarty') ) { // try and autoload it
        // if it doesn't autoload, fall back to letting Kohana find the bundled version
        $file = Kohana::find_file('vendor', 'smarty/libs/Smarty.class');
        require_once($file);
        // save the location in case we have more than one Smarty version around
        self::$_smarty_path = realpath(dirname($file)).DIRECTORY_SEPARATOR;
      }
    }

    // instantiate the prototype Smarty object
    $smarty = new Smarty;
    self::$_smarty_prototype = $smarty;

    // set up the prototype with options from config file
    foreach ( $config->smarty_config as $key => $value ) {
      $smarty->$key = $value;
    }

    // deal with config options that are not simple properties
    $smarty->php_handling = constant($config->php_handling);

    // add the path to the plugins for the located Smarty distro
    $smarty->addPluginsDir(self::$_smarty_path.'plugins');
    // add views directories for all loaded modules (including Smarty3)
    $dirs = array();
    foreach ( Kohana::modules() as $dir ) {
      $dirs[] = "{$dir}views";
    }
    $smarty->addTemplateDir($dirs);

    if ( $config->check_dirs ) {
      // check we can write to the compiled templates directory
      if ( !is_writeable($smarty->compile_dir) ) {
        self::create_dir($smarty->compile_dir, 'Smarty compiled template');
      }

      // if smarty caching is enabled, check we can write to the cache directory
      if ( $smarty->caching && !is_writeable($smarty->cache_dir) ) {
        self::create_dir($smarty->cache_dir, 'Smarty cache');
      }
    }

    // now assign useful globals
    $smarty->assignGlobal('base_url', URL::base());
    $smarty->assignGlobal('helper', new Smarty_Helper);

    $bound = View::$_global_bound_variables;
    // register any globals
    foreach ( View::$_global_data as $key=>$value ) {
      if ( isset($bound[$key]) ) {
        Smarty::$global_tpl_vars[$key] = new Smarty_variable($value);
        Smarty::$global_tpl_vars[$key]->value = &$value;
      } else {
        $smarty->assignGlobal($key, $value);
      }
    }

    // and register useful plugins

    // add to registered template engines
    View::$_smarty_is_loaded = TRUE;

    // set timing for benchmark
    self::$_init_time = microtime(TRUE) - $time;
  }
  return self::$_smarty_prototype;
}

/**
 * Sets the view filename.
 *
 *     $view->set_filename($file);
 *
 * @param   string  view filename
 * @return  View
 * @throws  Kohana_View_Exception
 */
public function set_filename($file) {
    if ( !View::is_smarty_template($file) ) {
      throw new Kohana_Exception('Cannot use set_filename to change from a Smarty template to :tpl.',
        array(':tpl' => $file));
    }
    // $ext parameter set to '' for smarty .tpl extension in filename
    if ( ($path = Kohana::find_file('views', $file, ''))===FALSE ) {
      // allow absolute (or relative) path to specify Smarty template
      if ( file_exists($file) ) {
        $path = $file;
      } else {
        throw new Kohana_View_Exception('The requested view :file could not be found', array(
          ':file' => $file,
        ));
      }
}

  // Store the file path locally
  $this->_file = $path;

  return $this;
}

/**
 * Renders the view object to a string. Global and local data are merged
 * and extracted to create local variables within the view file.
 *
 *     $output = $view->render();
 *
 * [!!] Global variables with the same key name as local variables will be
 * overwritten by the local variable.
 *
 * @param    string  view filename
 * @return   string
 * @throws   Kohana_View_Exception
 * @uses     View::capture
 */
public function render($file = NULL) {
  if ($file !== NULL) {
    $this->set_filename($file);
  }

  if (empty($this->_file)) {
    throw new Kohana_View_Exception('You must set the file to use within your view before rendering');
  }

  return $this->_smarty->fetch($this->_file);
}

/**
 * Assigns a variable by name. Assigned values will be available as a
 * variable within the view file:
 *
 *     // This value can be accessed as $foo within the view
 *     $view->set('foo', 'my value');
 *
 * You can also use an array to set several values at once:
 *
 *     // Create the values $food and $beverage in the view
 *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
 *
 * @param   string   variable name or an array of variables
 * @param   mixed    value
 * @return  $this
 */
public function set($key, $value = NULL) {
  $this->_smarty->assign($key, $value);
  return $this;
}

/**
 * Returns some benchmarking information.
 *
 * @return  array  see code
 */
public function stats() {

  return array(
    'clone_count'      => self::$_clone_count,      // how many times the Smarty object has been cloned
    'clone_time'       => $this->_clone_time,       // time spent cloning this Smarty object
    'init_time'        => self::$_init_time,        // how long it took to load Smarty
    'total_clone_time' => self::$_total_clone_time, // total time spent cloning the Smarty object
  );
  
}

} // End View_Smarty

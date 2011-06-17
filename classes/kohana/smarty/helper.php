<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Provides interface to various Kohana elements for Smarty templates
 *
 * @package    Smarty3
 * @author     Mr Anchovy
 * @copyright  (c) 2011 Mr Anchovy
 * @license    http://kohanaframework.org/license
 */
class Kohana_Smarty_Helper {

/**
 * Report memory usage
 *
 * @return  integer  total Kohana memory usage in bytes
 */
public function memory() {
  return memory_get_usage() - KOHANA_START_MEMORY;
}

/**
 * Report execution statistics
 *
 * @param   string  vsprintf pattern
 *          NULL    return an array
 * @return  string  formatted stats using $pattern
 * @return  array   0 => run time in milliseconds
 *                  1 => memory used in MB
 *                  2 => number of files included
 */
public function stats($pattern=NULL) {
  $stats = array(
    (microtime(TRUE) - KOHANA_START_TIME) * 1000,
    (memory_get_usage() - KOHANA_START_MEMORY) / 1048576,
    count(get_included_files()),
  );
  if ( $pattern===NULL ) {
    return $stats;
  } else {
    return vsprintf($pattern, $stats);
  }
}

/**
 * Report run time
 *
 * @return  real  total run time in seconds
 */
public function time() {
  return microtime(TRUE) - KOHANA_START_TIME;
}

}

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

protected static $marks = array();

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
public function stats($pattern=NULL, $mark=NULL, $reset=NULL) {
  $lap = array(
    microtime(TRUE),
    memory_get_usage(),
    count(get_included_files()),
  );

  $start = FALSE;

  if ( $mark!==NULL ) {
    if ( isset(Smarty_Helper::$marks[$mark]) ) {
      // the mark is set so use it
      $start = Smarty_Helper::$marks[$mark];
      if ( $reset===TRUE ) {
        // only reset it if we have been asked to
        Smarty_Helper::$marks[$mark] = $lap;
      }
    } else {
      // the mark is not set so set it ignoring $reset
      Smarty_Helper::$marks[$mark] = $lap;
    }
  }

  if ( $start===FALSE ) {
    $start = array (
      KOHANA_START_TIME,
      KOHANA_START_MEMORY,
      0,
    );
  }

  $stats = array(
    ($lap[0] - $start[0]) * 1000,
    ($lap[1] - $start[1]) / 1048576,
    $lap[2] - $start[2]
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

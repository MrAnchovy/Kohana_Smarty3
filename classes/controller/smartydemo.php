<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Demonstration controller.
 *
 * @package    Smarty3
 * @author     Mr Anchovy
 * @copyright  (c) 2011 Mr Anchovy
 * @license    http://kohanaframework.org/license
 */
class Controller_SmartyDemo extends Controller_Template {

public $template = 'smarty_demo_page.tpl';

/**
 * Populate and render the demo page
 */
function action_index() {
  $view = View::factory('smarty_demo.tpl');

  $view->var = 'A variable';
  $view->things = array(
    'First'  => 'One',
    'Second' => 'Two',
    'Third'  => 'Three' );

  // we can use the Smarty object if we want!
  $view->smarty()->assign('direct', 'Smarty');

  $this->template->title = 'Demo Page' ;
  $this->template->content = $view->render();
  $this->template->versions = array(
    'smarty' => str_replace('Smarty-', '', Smarty::SMARTY_VERSION),
    'kohana' => Kohana::VERSION . ' ' . Kohana::CODENAME,
    'module' => Smarty_View::VERSION,
    'php'    => phpversion(),
    'server' => arr::get($_SERVER, 'SERVER_SOFTWARE'),
  );

}

}

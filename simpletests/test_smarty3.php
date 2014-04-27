<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package    Smarty
 * @category   Tests
 * @author     Mr Anchovy
 * @copyright  (c) 2011 Mr Anchovy
 * @license    http://opensource.org/licenses/ISC
 */
class Test_Smarty3 extends UnitTestCase {

function test_php_view_functions_still_work() {
  // IMPORTANT do this first, before the Smarty class autoloader is in scope
  try {
    $random = md5(microtime());
    View::bind_global('global_bound_variable', $random);
    View::set_global('global_variable', $random);
    $view = View::factory('smarty_test/test_php');
    $view->variable = $random;
    $view->bind('bound_variable', $random);
    $ok = TRUE;
  } catch (Exception $e) {
    $ok = FALSE;
    throw $e;
  }
  $this->assertTrue($ok, 'There should be no exceptions');
}

function test_can_load_smarty_template() {
  // these templates are in smarty3/views
  $view = View::factory('smarty_test/test.tpl');
  $this->assertIsA($view, 'Smarty_View', 'Should be a Smarty_View object|%s');
}

function test_can_load_php_template() {
  $view = View::factory('smarty_test/test_php');
  $this->assertIsA($view, 'View', 'Should be a View object|%s');
}

function test_can_load_smarty_template_with_legacy_syntax() {
  $view = View::factory('smarty:smarty_test/test');
  $this->assertIsA($view, 'Smarty_View', 'Should be a Smarty_View object|%s');
}

function test_can_load_smarty_with_no_template() {
  $view = View::factory('smarty:');
  $this->assertIsA($view, 'Smarty_View', 'Should be a Smarty_View object|%s');
}

function test_can_load_smarty_template_from_absolute_path() {
  $view = View::factory(dirname(__FILE__).'/templates/test_absolute.tpl');
  $this->assertIsA($view, 'Smarty_View', 'Should be a Smarty_View object|%s');
}

function test_implicit_variable_assignment() {
  $view = View::factory('smarty_test/test.tpl');
  // use random values throughout to avoid caching and/or object persistence issues
  $random = md5(microtime());
  $view->variable = $random;
  $output = $view->render();
  $this->assertPattern("/Variable \[$random\]/", $output, "Output should contain Variable [$random]|%s");
}

function test_variable_assignment_with_smarty_assign() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->smarty()->assign('variable', $random);
  $output = $view->render();
  $this->assertPattern("/Variable \[$random\]/", $output, "Output should contain Variable [$random]|%s");
}

function test_smarty_assign_creates_view_property() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->smarty()->assign('variable', $random);
  $this->assertIdentical($view->variable, $random, '$view->variable'." should equal [$random]|%s");
}

function test_smarty_local_variable_is_local() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->variable = $random;
  $view2 = View::factory('smarty_test/test2.tpl');
  $output = $view2->render();
  $this->assertNoPattern("/$random/", $output, "Output should not contain $random|%s");
}

function test_global_view_variable_is_global_in_smarty() {
  $random = md5(microtime());
  View::set_global('global_variable', $random);
  $view = View::factory('smarty_test/test.tpl');
  $output = $view->render();
  $this->assertPattern("/Global variable \[$random\]/", $output, "Output should contain Global variable [$random]|%s");
}

function test_global_view_variable_is_global_in_php() {
  $random = md5(microtime());
  View::set_global('global_variable', $random);
  $view = View::factory('smarty_test/test_php');
  $output = $view->render();
  $this->assertPattern("/PHP Global variable \[$random\]/", $output, "Output should contain PHP Global variable [$random]|%s");
}

function test_smarty_bound_variable_is_bound() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->bind('bound_variable', $random);
  $random = md5($random);
  $output = $view->render();
  $this->assertPattern("/Bound variable \[$random\]/", $output, "Output should contain Bound variable [$random]|%s");
}

function test_smarty_bound_variable_is_local() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->bind('variable', $random);
  $random = md5($random);
  $view2 = View::factory('smarty_test/test2.tpl');
  $output = $view2->render();
  $this->assertNoPattern("/$random/", $output, "Output should not contain $random|%s");
}

function test_global_bound_view_variable_is_global_and_bound_in_smarty() {
  $random = md5(microtime());
  View::bind_global('global_variable', $random);
  $view = View::factory('smarty_test/test.tpl');
  $random = md5($random);
  $output = $view->render();
  $this->assertPattern("/Global variable \[$random\]/", $output, "Output should contain Global variable [$random]|%s");
}

function test_global_bound_view_variable_is_global_and_bound_in_php() {
  $random = md5(microtime());
  View::bind_global('global_variable', $random);
  $view = View::factory('smarty_test/test_php');
  $random = md5($random);
  $output = $view->render();
  $this->assertPattern("/PHP Global variable \[$random\]/", $output, "Output should contain PHP Global variable [$random]|%s");
}

function test_smarty_include() {
  $view = View::factory('smarty_test/include.tpl');
  $view->variable = $random = md5(microtime());
  $output = $view->render();
  $this->assertPattern("/Parent template provided \[$random\]/", $output, "Output should contain 'Parent template provided [$random]'|%s");
}

function test_smarty_include_different_module() {
  $view = View::factory('smarty_test/test_view_path.tpl');
  $view->variable = $random = md5(microtime());
  $output = $view->render();
  $this->assertPattern("/Template in simpletest\/views \[$random\]/", $output, "Output should contain 'Template in simpletest/views [$random']|%s");
}

function test_template_inheritance() {
  $view = View::factory('smarty_test/mypage.tpl');
  $output = $view->render();
  $this->assertPattern("/My HTML Page Body goes here/", $output, "Output should contain 'My HTML Page Body goes here|%s");
}

}

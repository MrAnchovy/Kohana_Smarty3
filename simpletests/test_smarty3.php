<?php

class Test_Smarty3 extends UnitTestCase {

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
  $view = View::factory(dirname(__FILE__).'/test_absolute.tpl');
  $this->assertIsA($view, 'Smarty_View', 'Should be a Smarty_View object|%s');
}

function test_implicit_variable_assignment() {
  $view = View::factory('smarty_test/test.tpl');
  // use random values throughout to avoid caching and/or object persistence issues
  $random = md5(microtime());
  $view->variable = $random;
  $output = $view->render();
  $this->assertPattern("/Variable \[$random\]/", $output, "Output should contain Variable [$random]");
}

function test_variable_assignment_with_smarty_assign() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->smarty()->assign('variable', $random);
  $output = $view->render();
  $this->assertPattern("/Variable \[$random\]/", $output, "Output should contain Variable [$random]");
}

function test_smarty_assign_creates_view_property() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->smarty()->assign('variable', $random);
  $this->assertIdentical($view->variable, $random, '$view->variable'." should equal [$random]");
}

function test_smarty_local_variable_is_local() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->variable = $random;
  $view2 = View::factory('smarty_test/test2.tpl');
  $output = $view2->render();
  $this->assertNoPattern("/$random/", $output, "Output should not contain $random");
}

function test_global_view_variable_is_global_in_smarty() {
  $random = md5(microtime());
  View::set_global('global_variable', $random);
  $view = View::factory('smarty_test/test.tpl');
  $output = $view->render();
  $this->assertPattern("/Global variable \[$random\]/", $output, "Output should contain Global variable [$random]");
}

function test_global_view_variable_is_global_in_php() {
  $random = md5(microtime());
  View::set_global('global_variable', $random);
  $view = View::factory('smarty_test/test_php');
  $output = $view->render();
  $this->assertPattern("/PHP Global variable \[$random\]/", $output, "Output should contain PHP Global variable [$random]");
}

function test_smarty_bound_variable_is_bound() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->bind('bound_variable', $random);
  $random = md5($random);
  $output = $view->render();
  $this->assertPattern("/Bound variable \[$random\]/", $output, "Output should contain Bound variable [$random]");
}

function test_smarty_bound_variable_is_local() {
  $view = View::factory('smarty_test/test.tpl');
  $random = md5(microtime());
  $view->bind('variable', $random);
  $random = md5($random);
  $view2 = View::factory('smarty_test/test2.tpl');
  $output = $view2->render();
  $this->assertNoPattern("/$random/", $output, "Output should not contain $random");
}

}



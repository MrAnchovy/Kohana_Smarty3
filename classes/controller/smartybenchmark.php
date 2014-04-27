<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Demonstration controller.
 *
 * @package    Smarty3
 * @author     Mr Anchovy
 * @copyright  (c) 2011 Mr Anchovy
 * @license    http://kohanaframework.org/license
 */
class Controller_SmartyBenchmark extends Controller {

/**
 * Populate and render a benchmark page
 */
function action_index() {

  // load the forum
  $forum = array(
    'id' => 1,
    'title' => 'My demo forum',
  );
  // load the messages
  $i = 0;
  while ( $i < 100 ) {
    $message = array(
      'id' => $i,
      'subject' => "Random message subject $i",
      'body' =>  "Random message body $i",
      'date' =>  time(),
      'author' =>  array(
        'id' => 123,
        'name' => 'Author',
        'link' => 'users/profile/123',
        'image' => array(
          'height' => 100,
          'width' => 100,
          'url' => 'users/image/123.png',
          'title' => 'Author\'s avatar',
        ),
      ),
    );

    if ( $i==10 ) {
      $message['class'] = 'highlight';
    }

    $messages[] = $message;
    $i++;
  }

  $timer = new Smarty_Helper();

  $_trials = array(
     array(
      'name' => 'Smarty2',
      'template' => 'demo/forum_forum.tpl',
    ),
   array(
      'name' => 'PHP',
      'template' => 'demo/forum_forum',
    ),
    array(
      'name' => 'Smarty',
      'template' => 'demo/forum_forum.tpl',
    ),
    array(
      'name' => 'PHP2',
      'template' => 'demo/forum_forum',
    ),
  );

  if ( mt_rand(0, 1)==0 ) {
    $trials = array( $_trials[0], $_trials[1] );
  } else {
    $trials = array( $_trials[1], $_trials[0] );
  }

  foreach ( $trials as $id=>$trial ) {
    $timer->stats(FALSE, $trial['name']);
    $view = View::factory($trial['template']);
    $view->timer = $timer;
    $view->time_factory = $timer->stats(FALSE, $trial['name']);
    $view->messages = $messages;
    $view->forum = $forum;
    $trials[$id]['html'] = $view->render();
    $trials[$id]['stats'] =  $timer->stats($trial['name'].': %3$d files using %2$.2fMB in %1$.1fms', $trial['name']);
  }

  $this->template = View::factory('smarty_demo_page.tpl');
  $this->template->title = 'Benchmarking';
/*  $this->template->content = View::factory('demo/benchmark.tpl')->set(
    array(
      'trials' => $trials,
      'stats' => $stats,
    ))->render(); */
  $view = View::factory('demo/benchmark.tpl');
  $view->trials = $trials;
  $this->template->content = $view->render();
  $this->get_page_footer();
  $this->response->body($this->template->render());
}


function get_page_footer() {
  $this->template->versions = array(
    'smarty' => str_replace('Smarty-', '', Smarty::SMARTY_VERSION),
    'kohana' => Kohana::VERSION . ' ' . Kohana::CODENAME,
    'module' => Smarty_View::VERSION,
    'php'    => phpversion(),
    'server' => arr::get($_SERVER, 'SERVER_SOFTWARE'),
  );
  return $this;
}


}
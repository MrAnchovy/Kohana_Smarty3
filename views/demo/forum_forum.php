
<?php $timer->stats(false, 'php_template') ?>

<div class="forumhead forum-<?php echo $forum['id'] ?>">
  <h3 class="<?php echo $forum['title'] ?>">Messages</h3>

<ul class="forummessages forum-<?php echo $forum['id'] ?>">
<?php foreach ( $messages as $message ) { ?>
  <?php if ( empty($message['class']) ) { ?>
  <li id="msg-<?php echo $message['id'] ?>" class="msgwrap <?php echo $cycle = isset($cycle) ? ($cycle=='odd' ? 'even' : 'odd') : 'odd'; ?>">
  <?php } else { ?>
  <li id="msg-{$message.id}" class="msgwrap {cycle name=cycle_messages values='odd,even'} {$message.class}">
  <?php } ?>

Subject: <?php echo $message['subject'] ?><br />
id: <?php echo $message['author']['id'] ?><br />
User: <a href="<?php echo $message['author']['link'] ?>"><?php echo $message['author']['name'] ?></a><br />

<img src="<?php echo $message['author']['image']['url'] ?>" width="<?php echo $message['author']['image']['width'] ?>" height="<?php echo $message['author']['image']['height'] ?>" alt="<?php echo isset($message['author']['image']['alt']) ? $message['author']['image']['alt'] : $message['author']['name'] ?>" />

  </li>
<?php } ?>
</ul>

<?php echo $timer->stats('%3$d files using %2$.3fMB in %1$.1fms', 'php_template') ?>

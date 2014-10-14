<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:28:14 PM
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 */
/* @var $this View */
?>
<h1>Localization manager</h1>
<?php
echo $this->Html->link('Edit languages', array(
	'controller' => 'languages',
	'action' => 'index'
		), array('class' => 'btn'));
echo $this->Html->link('Edit messages', array(
	'controller' => 'messages',
	'action' => 'index'
		), array('class' => 'btn'));
echo $this->Html->link('Export all messages to files', array(
	'controller' => 'messages',
	'action' => 'export'
		), array('class' => 'btn btn-inverse'), "Are you sure want to export all messages?");

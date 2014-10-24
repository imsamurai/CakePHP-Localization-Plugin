<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:28:14 PM
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 * @package Localization.View
 */
?>
<h1><?= __('Localization manager'); ?></h1>
<?php
echo $this->Html->link(__('Edit languages'), array(
	'controller' => 'languages',
	'action' => 'index'
		), array('class' => 'btn'));
?>
<br>
<br>
<?php
echo $this->Html->link(__('Edit messages'), array(
	'controller' => 'messages',
	'action' => 'index'
		), array('class' => 'btn'));
?>
<br>
<br>
<?php
echo $this->Html->link(__('Export'), array(
	'controller' => 'localization',
	'action' => 'export'
		), array('class' => 'btn btn-info'));
?>
<br>
<br>
<?php
echo $this->Form->create('Localization', array(
	'type' => 'file',
	'url' => array(
		'action' => 'import',
		'controller' => 'localization',
	),
	'style' => 'margin:0px;display: inline;'
));
echo $this->Form->file('file');
echo $this->Form->button(__('Import'), array('class' => 'btn  btn-info'));
echo $this->Form->end();
?>
<br>
<br>
<?php
echo $this->Html->link(__('Export all messages to files'), array(
	'controller' => 'messages',
	'action' => 'export'
		), array('class' => 'btn btn-inverse'), __("Are you sure want to export all messages?"));


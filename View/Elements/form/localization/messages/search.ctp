<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Dec 23, 2013
 * Time: 5:50:39 PM
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 * @package Localization.View
 */
/* @var $this View */

echo $this->Form->create('Message', array(
	'novalidate',
	'class' => 'well form-search',
	'type' => 'get',
	'url' => array(
		'action' => 'index',
		'controller' => 'messages'
	)
));
?>
<div style="float:left;width:400px;margin-right:15px;">
	<div class="div-right">
		<?= $this->Form->input('id', array('type' => 'number')); ?>
	</div>
	<div class="div-right">
		<?=
		$this->Form->input('js', array(
			'options' => array(
				true => 'yes',
				false => 'no'
			),
			'type' => 'select',
			'empty' => 'both'
		));
		?>
	</div>
	<div class="div-right">
		<?= $this->Form->input('name'); ?>
	</div>
	<div class="div-right">
		<?= $this->Form->input('file'); ?>
	</div>
</div>
<div style="float:left;width:300px;">
	<div class="div-right">
		<?= $this->Form->input('modified', array('class' => 'input-large daterangepicker', 'type' => 'text')) ?>
	</div>
	<div class="div-right">
		<?= $this->Form->input('created', array('class' => 'input-large daterangepicker', 'type' => 'text')) ?>
	</div>
	<div class="div-right">
		<?=
		$this->Form->input('not_translated_language_id', array(
			'type' => 'select',
			'multiple' => true,
			'options' => $languages
		));
		?>
	</div>
</div>
<div style="clear:left;"></div>
<div style="float:left;width:415px;">
	<div class="div-right">
		<?= $this->Form->button('Search', array('class' => 'btn btn-primary', 'div' => false)); ?>
		<?= $this->Html->link('Clear', array('action' => 'index'), array('class' => 'btn', 'id' => "btn-clear")); ?>
	</div>
</div>
<div style="clear:left;"></div>
<?php
echo $this->Form->end();

<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.10.2014
 * Time: 17:25:16
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 */
/* @var $this IDEView */
echo $this->Form->create('Language', array(
	'class' => 'well form-search',
	'type' => 'post',
	'url' => array(
		'action' => empty($id) ? 'create' : 'edit',
		'controller' => 'languages',
		empty($id) ? null : $id
	)
));
echo $this->Form->input('name');
echo $this->Form->input('code');
echo $this->Form->button('Save', array('class' => 'btn btn-primary', 'div' => false));
echo $this->Form->end();
echo $this->Html->link('language codes list', 'http://www.loc.gov/standards/iso639-2/php/code_list.php', array('target' => '_blank'));

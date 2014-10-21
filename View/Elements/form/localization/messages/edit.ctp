<?php
/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 08.10.2014
 * Time: 17:25:16
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 * @package Localization.View.Element
 */
/* @var $this View */
echo $this->Form->create('Message', array(
	'class' => 'well form-search',
	'type' => 'post',
	'url' => array(
		'action' => empty($id) ? 'create' : 'edit',
		'controller' => 'messages',
		empty($id) ? null : $id
	)
));
echo $this->Form->hidden('id');
echo $this->Form->input('name', array('class' => 'input-xxlarge'));
$modified = $this->request->data("Message.modified");
$created = $this->request->data("Message.created");
if ($modified) {
	echo $this->Html->div('message-modified', __('Created') . ': ' . $this->Time->timeAgoInWords($created));
	echo $this->Html->div('message-modified', __('Last modified') .': ' .$this->Time->timeAgoInWords($modified));
}
echo $this->Form->input('js');

echo $this->Html->tag('h4', __('References'));
echo $this->element('localization/messages/references', array(
	'references' => $this->request->data("References")
));

echo $this->Html->tag('h4', __('Translations'));
foreach ($languages as $languageId => $languageName) {
	echo $this->Form->hidden("Translations.$languageId.id");
	echo $this->Form->hidden("Translations.$languageId.message_id", array('default' => empty($id) ? null : $id));
	echo $this->Form->hidden("Translations.$languageId.language_id", array('value' => $languageId));
	echo $this->Form->input("Translations.$languageId.text", array(
		'label' => __("Translation to $languageName"),
		'class' => 'input-xxlarge'
	));
	$modified = $this->request->data("Translations.$languageId.modified");
	if ($modified) {
		echo $this->Html->div('message-modified',  __('Last modified') .': ' . $this->Time->timeAgoInWords($modified));
	}
}

echo $this->Form->button('Save', array('class' => 'btn btn-primary', 'div' => false));
echo $this->Form->end();
echo $this->Html->link(__('language codes list'), 'http://www.loc.gov/standards/iso639-2/php/code_list.php', array('target' => '_blank'));
?>
<style type="text/css">
	.message-modified, .message-noreference {
		color: #777;
		margin-bottom: 10px;
	}
	.message-reference {
		color: #777;
	}
</style>
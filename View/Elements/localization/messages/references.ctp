<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 17.10.2014
 * Time: 16:15:56
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 * @package Localization.View.Element
 */
if (!$references) {
	echo $this->Html->div('message-noreference', __('There is no references'));
} else {
	$referencesHtml = array();
	foreach ($references as $reference) {
		$name = basename($reference['file']) . ($reference['line'] > 0 ? ':' . $reference['line'] : '');
		$title = __("File") . ": " . ROOT . $reference['file'];
		if ($reference['line']) {
			$title .= "\n" . __("Line") . ": " . $reference['line'];
		}
		if ($reference['comment']) {
			$title .= "\n" . __("Comment") . ": " . $reference['comment'];
		}
		$referencesHtml[] = $this->Html->tag('span', $name, array('title' => $title, 'class' => 'message-reference'));
	}
	echo implode(', ', $referencesHtml);
}

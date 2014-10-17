<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 17.10.2014
 * Time: 16:15:56
 * Format: http://book.cakephp.org/2.0/en/views.html
 * 
 */
if (!$references) {
	echo $this->Html->div('message-noreference', 'There is no references');
} else {
	$referencesHtml = array();
	foreach ($references as $reference) {
		$name = basename($reference['file']) . ($reference['line'] > 0 ? ':' . $reference['line'] : '');
		$title = "File: " . ROOT . "{$reference['file']}\nLine: {$reference['line']}\nComment: {$reference['comment']}";
		$referencesHtml[] = $this->Html->tag('span', $name, array('title' => $title, 'class' => 'message-reference'));
	}
	echo implode(', ', $referencesHtml);
}

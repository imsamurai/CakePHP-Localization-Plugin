<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:32:58 PM
 * Format: http://book.cakephp.org/2.0/en/views.html
 */
Configure::write('Pagination.pages', Configure::read('Pagination.pages') ? Configure::read('Pagination.pages') : 10);
$config = (array)Configure::read('Localization');
$config += array(
	'header' => "# Base file\n" .
	"# Copyright (C)\n" . //copyright
	"# localization plugin by imsamurai, 2014\n" .
	"#\n" .
	"msgid \"\"\n" .
	"msgstr \"\"\n" .
	"\"POT-Creation-Date: " . date('c', time()) . "\\n\"\n" .
	"\"PO-Revision-Date: " . date('c', time()) . "\\n\"\n" .
	"\"MIME-Version: 1.0\\n\"\n" .
	"\"Content-Type: text/plain; charset=UTF-8\\n\"\n" .
	"\"Content-Transfer-Encoding: 8bit\\n\"\n" .
	"\n",
	'path' => APP . 'Locale' . DS . '%s' . DS . 'LC_MESSAGES' . DS,
	'jsPath' => APP . 'webroot' . DS . 'js' . DS . 'Locale' . DS . '%s' . DS . 'LC_MESSAGES' . DS,
	'jsTemplate' => "function \__(m){var l=%s;return l[m]?l[m]:m;}"
);
Configure::write('Localization', $config);
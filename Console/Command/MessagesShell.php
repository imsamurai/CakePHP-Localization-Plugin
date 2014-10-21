<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 17.10.2014
 * Time: 14:19:49
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#creating-a-shell
 */
App::uses('AppShell', 'Console/Command');

/**
 * MessagesShell
 * 
 * @package Localization
 * @subpackage Console.Command
 */
class MessagesShell extends AppShell {

	/**
	 * Contains tasks to load and instantiate
	 *
	 * @var array
	 */
	public $tasks = array(
		'Extract' => array(
			'className' => 'Localization.LocalizationExtract'
		)
	);

	/**
	 * {@inheritdoc}
	 * 
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		return parent::getOptionParser()
						->description(__('Message Shell'))
						->addSubcommand('extract', array(
							'help' => __('Extract the po translations from your application'),
							'parser' => $this->Extract->getOptionParser()
		));
	}

}

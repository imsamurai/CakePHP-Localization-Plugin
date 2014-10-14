<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:36:02 PM
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('LocalizationAppModel', 'Localization.Model');

/**
 * Localization Translation Model
 * 
 * @package Localization
 * @subpackage Model
 */
class Translation extends LocalizationAppModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $virtualFields = array(
		'translated' => 'text!=""'
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Language' => array(
			'className' => 'Localization.Language'
		),
		'Message' => array(
			'className' => 'Localization.Message'
		)
	);

}

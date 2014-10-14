<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:36:02 PM
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('LocalizationAppModel', 'Localization.Model');

/**
 * Localization Language Model
 * 
 * @package Localization
 * @subpackage Model
 */
class Language extends LocalizationAppModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $validate = array(
		'code' => array(
			'alpha3' => array(
				'rule' => '/^[a-z]{3}$/',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Code must have 3 letters only (lower case)'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Code must be unique'
			),
		),
		'name' => array(
			'alphanumeric' => array(
				'rule' => 'alphanumeric',
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Name must be alphanumeric'
			),
			'length' => array(
				'rule' => array('maxLength', 100),
				'message' => 'Name length must be less or equal to 100'
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Name must be unique'
			),
		)
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Translations' => array(
			'className' => 'Localization.Translation'
		)
	);

}

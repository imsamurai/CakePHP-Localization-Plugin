<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 10.10.2014
 * Time: 12:40:08
 * Format: http://book.cakephp.org/2.0/en/models.html
 */

/**
 * LocalizationAppModel Model
 * 
 * @package Localization
 * @subpackage Model
 */
abstract class LocalizationAppModel extends AppModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $useDbConfig = 'localization';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $actsAs = array('Containable');
	
	/**
	 * {@inheritdoc}
	 *
	 * @var int
	 */
	public $recursive = -1;

}

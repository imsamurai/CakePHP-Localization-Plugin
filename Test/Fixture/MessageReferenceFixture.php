<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 */

/**
 * LocalizationMessageReferenceFixture
 *
 * @package LocalizationTest
 * @subpackage Fixture
 */
class MessageReferenceFixture extends CakeTestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'message_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'file' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'line' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'index'),
		'comment' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 200, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id_UNIQUE' => array('column' => 'id', 'unique' => 1),
			'message_id' => array('column' => 'message_id', 'unique' => 0),
			'file' => array('column' => 'file', 'unique' => 0),
			'line' => array('column' => 'line', 'unique' => 0),
			'comment' => array('column' => 'comment', 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'MyISAM')
	);

	/**
	 * Records
	 *
	 * @var array
	 */
	public $records = array(
		array(
			'id' => '1',
			'message_id' => '1',
			'file' => '/vendor/cakephp/cakephp/lib/Cake/Cache/Cache.php',
			'line' => '173',
			'comment' => ''
		),
		array(
			'id' => '2',
			'message_id' => '1',
			'file' => '/app/index.php',
			'line' => '123',
			'comment' => ''
		),
		array(
			'id' => '3',
			'message_id' => '2',
			'file' => '/app/webroot/index.php',
			'line' => '2',
			'comment' => ''
		),
	);

}

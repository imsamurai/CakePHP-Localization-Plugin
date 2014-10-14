<?php

/**
 * LocalizationLanguageFixture
 *
 * @package LocalizationTest
 * @subpackage Fixture
 */
class LanguageFixture extends CakeTestFixture {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $useDbConfig = 'test';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'primary'),
		'code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 3, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 100, 'key' => 'unique', 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id_UNIQUE' => array('column' => 'id', 'unique' => 1),
			'name_unique' => array('column' => 'name', 'unique' => 1),
			'full_unique' => array('column' => array('name', 'code'), 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_bin', 'engine' => 'MyISAM')
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $records = array(
		array(
			'id' => '1',
			'code' => 'ukr',
			'name' => 'Ukrainian',
			'created' => '2014-10-09 03:37:49',
			'modified' => '2014-10-09 03:38:27'
		),
		array(
			'id' => '2',
			'code' => 'eng',
			'name' => 'English',
			'created' => '2014-10-09 03:38:35',
			'modified' => '2014-10-09 03:38:35'
		),
		array(
			'id' => '3',
			'code' => 'deu',
			'name' => 'German',
			'created' => '2014-10-09 03:38:42',
			'modified' => '2014-10-09 03:39:29'
		),
		array(
			'id' => '4',
			'code' => 'jpn',
			'name' => 'Japanese',
			'created' => '2014-10-09 03:39:50',
			'modified' => '2014-10-09 03:41:06'
		),
		array(
			'id' => '5',
			'code' => 'urd',
			'name' => 'Urdu',
			'created' => '2014-10-10 06:26:41',
			'modified' => '2014-10-10 06:26:41'
		),
	);

}

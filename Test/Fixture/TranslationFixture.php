<?php

/**
 * LocalizationTranslationFixture
 *
 */
class TranslationFixture extends CakeTestFixture {

	/**
	 * Fields
	 *
	 * @var array
	 */
	public $fields = array(
		'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary'),
		'message_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index'),
		'language_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 10, 'unsigned' => true, 'key' => 'index'),
		'text' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8_bin', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'id_UNIQUE' => array('column' => 'id', 'unique' => 1),
			'message_id' => array('column' => 'message_id', 'unique' => 0),
			'language_id' => array('column' => 'language_id', 'unique' => 0),
			'not_translated_index' => array('column' => array('message_id', 'language_id'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_bin', 'engine' => 'MyISAM')
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
			'language_id' => '1',
			'text' => 'Повідомлення 1',
			'created' => '2014-10-13 02:25:03',
			'modified' => '2014-10-14 05:32:28'
		),
		array(
			'id' => '2',
			'message_id' => '1',
			'language_id' => '2',
			'text' => 'Message 1',
			'created' => '2014-10-13 02:25:03',
			'modified' => '2014-10-14 05:32:28'
		),
		array(
			'id' => '3',
			'message_id' => '1',
			'language_id' => '3',
			'text' => 'Nachricht 1',
			'created' => '2014-10-13 02:25:03',
			'modified' => '2014-10-14 05:32:28'
		),
		array(
			'id' => '4',
			'message_id' => '1',
			'language_id' => '4',
			'text' => 'メッセージ1',
			'created' => '2014-10-13 02:25:03',
			'modified' => '2014-10-14 05:32:28'
		),
		array(
			'id' => '5',
			'message_id' => '1',
			'language_id' => '5',
			'text' => 'پیغام 1',
			'created' => '2014-10-13 02:25:03',
			'modified' => '2014-10-14 05:32:28'
		),
		array(
			'id' => '6',
			'message_id' => '2',
			'language_id' => '1',
			'text' => 'Грудень',
			'created' => '2014-10-13 04:03:23',
			'modified' => '2014-10-13 04:03:23'
		),
		array(
			'id' => '7',
			'message_id' => '2',
			'language_id' => '2',
			'text' => 'December',
			'created' => '2014-10-13 04:03:23',
			'modified' => '2014-10-13 04:03:23'
		),
		array(
			'id' => '8',
			'message_id' => '2',
			'language_id' => '3',
			'text' => 'Dezember',
			'created' => '2014-10-13 04:03:23',
			'modified' => '2014-10-14 05:33:11'
		),
		array(
			'id' => '9',
			'message_id' => '2',
			'language_id' => '4',
			'text' => '12月',
			'created' => '2014-10-13 04:03:23',
			'modified' => '2014-10-14 05:33:11'
		),
		array(
			'id' => '10',
			'message_id' => '2',
			'language_id' => '5',
			'text' => 'دسمبر',
			'created' => '2014-10-13 04:03:23',
			'modified' => '2014-10-14 05:33:11'
		),
	);

}

<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 14.10.2014
 * Time: 15:53:09
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('Message', 'Localization.Model');

/**
 * MessageTest
 * 
 * @package LocalizationTest
 * @subpackage Model
 */
class MessageTest extends CakeTestCase {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.Localization.Language',
		'plugin.Localization.Message',
		'plugin.Localization.Translation',
		'plugin.Localization.MessageReference',
	);

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		Configure::write('Localization.header', "# Base file\n" .
				"# Copyright (C)\n" . //copyright
				"# localization plugin by imsamurai, 2014\n" .
				"#\n" .
				"msgid \"\"\n" .
				"msgstr \"\"\n" .
				"\"POT-Creation-Date: 2014-10-14T17:03:25+03:00\\n\"\n" .
				"\"PO-Revision-Date: 2014-10-14T17:03:25+03:00\\n\"\n" .
				"\"MIME-Version: 1.0\\n\"\n" .
				"\"Content-Type: text/plain; charset=UTF-8\\n\"\n" .
				"\"Content-Transfer-Encoding: 8bit\\n\"\n" .
				"\n");

		Configure::write('Localization.path', TMP . 'Locale' . DS . '%s' . DS . 'LC_MESSAGES' . DS);
		Configure::write('Localization.jsPath', TMP . 'Locale' . DS . '%s' . DS . 'LC_MESSAGES' . DS);
		Configure::write('Localization.jsTemplate', "function \$L(m){var l=%s;return l[m]?l[m]:m;}");
		$locale = TMP . 'Locale';
		`rm -rf $locale`;
	}

	/**
	 * Test data validation
	 * 
	 * @param array $data
	 * @param array $validationErrors
	 * 
	 * @dataProvider validationProvider
	 */
	public function testValidation(array $data, array $validationErrors) {
		$Message = ClassRegistry::init('Localization.Message');
		$Message->set($data);
		$this->assertSame(!$validationErrors, $Message->validates());
		$this->assertSame($validationErrors, $Message->validationErrors);
	}

	/**
	 * Data provider for testValidation
	 * 
	 * @return array
	 */
	public function validationProvider() {
		return array(
			//set #0
			array(
				//data
				array(),
				//validationErrors
				array(
					'name' => array(
						'Name should not be empty'
					)
				)
			),
			//set #1
			array(
				//data
				array(
					'name' => '_sdfsd_'
				),
				//validationErrors
				array()
			),
			//set #2
			array(
				//data
				array(
					'name' => 'message1'
				),
				//validationErrors
				array(
					'name' => array(
						'Name must be unique'
					)
				)
			),
			//set #3
			array(
				//data
				array(
					'name' => str_repeat('x', 201)
				),
				//validationErrors
				array(
					'name' => array(
						'Name length must be less or equal to 200'
					)
				)
			),
			//set #4
			array(
				//data
				array(
					'name' => str_repeat('x', 200)
				),
				//validationErrors
				array()
			),
		);
	}

	/**
	 * Test save associated with update only changed data
	 * 
	 * @param array $data
	 * @param array $dataFromDB
	 * @param array $dataToSave
	 * 
	 * @dataProvider saveAssociatedChangedProvider
	 */
	public function testSaveAssociatedChanged(array $data, array $dataFromDB, array $dataToSave) {
		$options = array('options');
		$id = $dataFromDB ? 1 : null;

		$Message = $this->getMockForModel('Localization.Message', array(
			'saveAssociated',
			'getById'
		));
		$Message->id = $id;
		
		if (!empty($data['References'])) {
			$Reference = $this->getMockForModel('Localization.MessageReference', array('deleteAll'));
			$Reference->expects($this->once())->method('deleteAll')
					->with(array(
						'message_id' => $id
							), false)
					->willReturn(true);
			$Message->References = $Reference;
		}

		if ($dataFromDB) {
			$Message->expects($this->once())->method('getById')->with($id)->willReturn($dataFromDB);
		}		

		$Message->expects($this->once())->method('saveAssociated')->with($dataToSave, $options)->willReturn(true);
		$this->assertTrue($Message->saveAssociatedChanged($data, $options));
	}

	/**
	 * Data provider for testSaveAssociatedChanged
	 * 
	 * @return array
	 */
	public function saveAssociatedChangedProvider() {
		return array(
			//set #0
			array(
				//data
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '1'
						),
						2 => array(
							'text' => '2'
						),
					)
				),
				//dataFromDB
				array(),
				//dataToSave
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '1'
						),
						2 => array(
							'text' => '2'
						),
					)
				)
			),
			//set #1
			array(
				//data
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '1'
						),
						2 => array(
							'text' => '2'
						),
					)
				),
				//dataFromDB
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '1'
						)
					)
				),
				//dataToSave
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						2 => array(
							'text' => '2'
						),
					)
				)
			),
			//set #2
			array(
				//data
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '111'
						),
						2 => array(
							'text' => '2'
						),
						3 => array(
							'text' => '3'
						),
					)
				),
				//dataFromDB
				array(
					'Message' => array(
						'name' => 'testmessage123'
					),
					'Translations' => array(
						1 => array(
							'text' => '1'
						),
						2 => array(
							'text' => '2'
						),
					)
				),
				//dataToSave
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '111'
						),
						3 => array(
							'text' => '3'
						),
					)
				)
			),
			//set #3
			array(
				//data
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '111'
						),
						2 => array(
							'text' => '2'
						),
						3 => array(
							'text' => '3'
						),
					),
					'References' => array(
						array(
							'file' => 'home.ctp',
							'line' => '30',
							'comment' => ''
						),
						array(
							'file' => 'Model.php',
							'line' => '',
							'comment' => 'validation message'
						)
					)
				),
				//dataFromDB
				array(
					'Message' => array(
						'name' => 'testmessage123'
					),
					'Translations' => array(
						1 => array(
							'text' => '1'
						),
						2 => array(
							'text' => '2'
						),
					),
					'References' => array(
						array(
							'file' => 'home.ctp',
							'line' => '29',
							'comment' => ''
						)
					)
				),
				//dataToSave
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '111'
						),
						3 => array(
							'text' => '3'
						),
					),
					'References' => array(
						array(
							'file' => 'home.ctp',
							'line' => '30',
							'comment' => ''
						),
						array(
							'file' => 'Model.php',
							'line' => '',
							'comment' => 'validation message'
						)
					)
				)
			),
		);
	}

	/**
	 * Test get message by id with translations
	 * 
	 * @param array $findResult
	 * @param array $result
	 * 
	 * @dataProvider getByIdProvider
	 */
	public function testGetById(array $findResult, array $result) {
		$id = 1;

		$Message = $this->getMockForModel('Localization.Message', array(
			'find'
		));

		$Message->expects($this->once())->method('find')->with('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => array('Translations', 'References')
		))->willReturn($findResult);

		$this->assertSame($result, $Message->getById($id));
	}

	/**
	 * Data provider for testGetById
	 * 
	 * @return array
	 */
	public function getByIdProvider() {
		return array(
			//set #0
			array(
				//findResult
				array(),
				//result
				array()
			),
			//set #1
			array(
				//findResult
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						0 => array(
							'text' => '1',
							'language_id' => '1'
						),
						1 => array(
							'text' => '3',
							'language_id' => '3'
						),
					)
				),
				//result
				array(
					'Message' => array(
						'name' => 'testmessage'
					),
					'Translations' => array(
						1 => array(
							'text' => '1',
							'language_id' => '1'
						),
						3 => array(
							'text' => '3',
							'language_id' => '3'
						),
					)
				)
			),
		);
	}

	/**
	 * Test export all messages
	 * 
	 * @param array $findData
	 * @param array $phpContent
	 * @param array $jsContent
	 * 
	 * @dataProvider exportProvider
	 */
	public function testExport($findData, array $phpContent, array $jsContent) {
		if ($findData !== 'fixture') {
			$Language = $this->getMockForModel('Localization.Language', array(
				'find'
			));

			$Language->expects($this->once())->method('find')->with('all', array(
				'contain' => array(
					'Translations' => array(
						'Message',
						'conditions' => array(
							'text !=' => ''
						)
					)
				)
			))->willReturn($findData);
		} else {
			$Language = ClassRegistry::init('Localization.Language');
		}
		$Message = ClassRegistry::init('Localization.Message');
		$Message->Translations->Language = $Language;
		$this->assertTrue($Message->export());
		foreach ($phpContent as $langCode => $content) {
			$this->assertSame($content, file_get_contents(sprintf(Configure::read('Localization.path') . 'default.po', $langCode)));
		}
		foreach ($jsContent as $langCode => $content) {
			$this->assertSame($content, file_get_contents(sprintf(Configure::read('Localization.jsPath') . 'default.js', $langCode)));
		}
	}

	/**
	 * Data provider for testExport
	 * 
	 * @return array
	 */
	public function exportProvider() {
		return array(
			//set #0
			array(
				//findData
				array(
					(int)0 => array(
						'Language' => array(
							'id' => '1',
							'code' => 'ukr',
							'name' => 'Ukrainian',
							'created' => '2014-10-09 03:37:49',
							'modified' => '2014-10-09 03:38:27'
						),
						'Translations' => array(
							(int)0 => array(
								'id' => '1',
								'message_id' => '1',
								'language_id' => '1',
								'text' => 'Повідомлення 1',
								'created' => '2014-10-13 02:25:03',
								'modified' => '2014-10-14 05:32:28',
								'translated' => '1',
								'Message' => array(
									'id' => '1',
									'name' => 'message1',
									'js' => false,
									'created' => '2014-10-13 02:25:03',
									'modified' => '2014-10-14 05:32:28'
								)
							),
							(int)1 => array(
								'id' => '6',
								'message_id' => '2',
								'language_id' => '1',
								'text' => 'Грудень',
								'created' => '2014-10-13 04:03:23',
								'modified' => '2014-10-13 04:03:23',
								'translated' => '1',
								'Message' => array(
									'id' => '2',
									'name' => 'december',
									'js' => true,
									'created' => '2014-10-13 04:03:23',
									'modified' => '2014-10-14 05:33:11'
								)
							)
						)
					),
					(int)1 => array(
						'Language' => array(
							'id' => '2',
							'code' => 'eng',
							'name' => 'English',
							'created' => '2014-10-09 03:38:35',
							'modified' => '2014-10-09 03:38:35'
						),
						'Translations' => array(
							(int)0 => array(
								'id' => '2',
								'message_id' => '1',
								'language_id' => '2',
								'text' => 'Message 1',
								'created' => '2014-10-13 02:25:03',
								'modified' => '2014-10-14 05:32:28',
								'translated' => '1',
								'Message' => array(
									'id' => '1',
									'name' => 'message1',
									'js' => false,
									'created' => '2014-10-13 02:25:03',
									'modified' => '2014-10-14 05:32:28'
								)
							),
							(int)1 => array(
								'id' => '7',
								'message_id' => '2',
								'language_id' => '2',
								'text' => 'December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December',
								'created' => '2014-10-13 04:03:23',
								'modified' => '2014-10-13 04:03:23',
								'translated' => '1',
								'Message' => array(
									'id' => '2',
									'name' => 'december',
									'js' => true,
									'created' => '2014-10-13 04:03:23',
									'modified' => '2014-10-14 05:33:11'
								)
							)
						)
					),
					(int)2 => array(
						'Language' => array(
							'id' => '3',
							'code' => 'deu',
							'name' => 'German',
							'created' => '2014-10-09 03:38:42',
							'modified' => '2014-10-09 03:39:29'
						),
						'Translations' => array(
							(int)0 => array(
								'id' => '3',
								'message_id' => '1',
								'language_id' => '3',
								'text' => 'Nachricht 1',
								'created' => '2014-10-13 02:25:03',
								'modified' => '2014-10-14 05:32:28',
								'translated' => '1',
								'Message' => array(
									'id' => '1',
									'name' => 'message1',
									'js' => false,
									'created' => '2014-10-13 02:25:03',
									'modified' => '2014-10-14 05:32:28'
								)
							),
							(int)1 => array(
								'id' => '8',
								'message_id' => '2',
								'language_id' => '3',
								'text' => 'Dezember',
								'created' => '2014-10-13 04:03:23',
								'modified' => '2014-10-14 05:33:11',
								'translated' => '1',
								'Message' => array(
									'id' => '2',
									'name' => 'december',
									'js' => true,
									'created' => '2014-10-13 04:03:23',
									'modified' => '2014-10-14 05:33:11'
								)
							)
						)
					),
					(int)3 => array(
						'Language' => array(
							'id' => '4',
							'code' => 'jpn',
							'name' => 'Japanese',
							'created' => '2014-10-09 03:39:50',
							'modified' => '2014-10-09 03:41:06'
						),
						'Translations' => array(
							(int)0 => array(
								'id' => '4',
								'message_id' => '1',
								'language_id' => '4',
								'text' => 'メッセージ1',
								'created' => '2014-10-13 02:25:03',
								'modified' => '2014-10-14 05:32:28',
								'translated' => '1',
								'Message' => array(
									'id' => '1',
									'name' => 'message1',
									'js' => false,
									'created' => '2014-10-13 02:25:03',
									'modified' => '2014-10-14 05:32:28'
								)
							),
							(int)1 => array(
								'id' => '9',
								'message_id' => '2',
								'language_id' => '4',
								'text' => '12月',
								'created' => '2014-10-13 04:03:23',
								'modified' => '2014-10-14 05:33:11',
								'translated' => '1',
								'Message' => array(
									'id' => '2',
									'name' => 'december',
									'js' => true,
									'created' => '2014-10-13 04:03:23',
									'modified' => '2014-10-14 05:33:11'
								)
							)
						)
					),
					(int)4 => array(
						'Language' => array(
							'id' => '5',
							'code' => 'urd',
							'name' => 'Urdu',
							'created' => '2014-10-10 06:26:41',
							'modified' => '2014-10-10 06:26:41'
						),
						'Translations' => array(
							(int)0 => array(
								'id' => '5',
								'message_id' => '1',
								'language_id' => '5',
								'text' => 'پیغام 1',
								'created' => '2014-10-13 02:25:03',
								'modified' => '2014-10-14 05:32:28',
								'translated' => '1',
								'Message' => array(
									'id' => '1',
									'name' => 'message1',
									'js' => false,
									'created' => '2014-10-13 02:25:03',
									'modified' => '2014-10-14 05:32:28'
								)
							),
							(int)1 => array(
								'id' => '10',
								'message_id' => '2',
								'language_id' => '5',
								'text' => 'دسمبر',
								'created' => '2014-10-13 04:03:23',
								'modified' => '2014-10-14 05:33:11',
								'translated' => '1',
								'Message' => array(
									'id' => '2',
									'name' => 'december',
									'js' => true,
									'created' => '2014-10-13 04:03:23',
									'modified' => '2014-10-14 05:33:11'
								)
							)
						)
					)
				),
				//phpContent
				array(
					'deu' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "Nachricht 1"

msgid "december"
msgstr "Dezember"

',
					'eng' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "Message 1"

msgid "december"
msgstr ""
"December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December Decem"
"ber December December December December December"

',
					'jpn' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "メッセージ1"

msgid "december"
msgstr "12月"

',
					'ukr' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "Повідомлення 1"

msgid "december"
msgstr "Грудень"

',
					'urd' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "پیغام 1"

msgid "december"
msgstr "دسمبر"

'
				),
				//jsContent
				array(
					'deu' => 'function $L(m){var l={"december":"Dezember"};return l[m]?l[m]:m;}',
					'eng' => 'function $L(m){var l={"december":"December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December December"};return l[m]?l[m]:m;}',
					'jpn' => 'function $L(m){var l={"december":"12\u6708"};return l[m]?l[m]:m;}',
					'ukr' => 'function $L(m){var l={"december":"\u0413\u0440\u0443\u0434\u0435\u043d\u044c"};return l[m]?l[m]:m;}',
					'urd' => 'function $L(m){var l={"december":"\u062f\u0633\u0645\u0628\u0631"};return l[m]?l[m]:m;}'
				)
			),
			//set #1
			array(
				//findData
				'fixture',
				//phpContent
				array(
					'deu' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "Nachricht 1"

msgid "december"
msgstr "Dezember"

',
					'eng' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "Message 1"

msgid "december"
msgstr "December"

',
					'jpn' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "メッセージ1"

msgid "december"
msgstr "12月"

',
					'ukr' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "Повідомлення 1"

msgid "december"
msgstr "Грудень"

',
					'urd' => '# Base file
# Copyright (C)
# localization plugin by imsamurai, 2014
#
msgid ""
msgstr ""
"POT-Creation-Date: 2014-10-14T17:03:25+03:00\n"
"PO-Revision-Date: 2014-10-14T17:03:25+03:00\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"

msgid "message1"
msgstr "پیغام 1"

msgid "december"
msgstr "دسمبر"

'
				),
				//jsContent
				array(
					'deu' => 'function $L(m){var l={"december":"Dezember"};return l[m]?l[m]:m;}',
					'eng' => 'function $L(m){var l={"december":"December"};return l[m]?l[m]:m;}',
					'jpn' => 'function $L(m){var l={"december":"12\u6708"};return l[m]?l[m]:m;}',
					'ukr' => 'function $L(m){var l={"december":"\u0413\u0440\u0443\u0434\u0435\u043d\u044c"};return l[m]?l[m]:m;}',
					'urd' => 'function $L(m){var l={"december":"\u062f\u0633\u0645\u0628\u0631"};return l[m]?l[m]:m;}'
				)
			)
		);
	}

}

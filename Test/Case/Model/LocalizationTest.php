<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 13.10.2014
 * Time: 18:03:06
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('Localization', 'Localization.Model');

/**
 * LocalizationTest
 * 
 * @property Localization $Localization Localization
 * 
 * @package LocalizationTest
 * @subpackage Model
 */
class LocalizationTest extends CakeTestCase {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		$Localization = ClassRegistry::init('Localization.Localization');
		$methods = array('find', 'saveAll', 'deleteAll');
		$Localization->Language = $this->getMockForModel('Localization.Language', $methods);
		$Localization->Message = $this->getMockForModel('Localization.Message', $methods);
		$Localization->MessageReference = $this->getMockForModel('Localization.MessageReference', $methods);
		$Localization->Translation = $this->getMockForModel('Localization.Translation', $methods);
		$this->Localization = $Localization;
	}

	/**
	 * Test clear all
	 */
	public function testClearAll() {
		$this->Localization->Language->expects($this->once())->method('deleteAll')->with(array(1 => 1), false)->willReturn(true);
		$this->Localization->Message->expects($this->once())->method('deleteAll')->with(array(1 => 1), false)->willReturn(true);
		$this->Localization->MessageReference->expects($this->once())->method('deleteAll')->with(array(1 => 1), false)->willReturn(true);
		$this->Localization->Translation->expects($this->once())->method('deleteAll')->with(array(1 => 1), false)->willReturn(true);
		$this->assertTrue($this->Localization->clearAll());
	}

	/**
	 * Test import
	 * 
	 * @param array $data
	 * 
	 * @dataProvider importProvider
	 */
	public function testImport(array $data) {
		$this->Localization->Language->expects($this->once())->method('deleteAll')->with(array(1 => 1), false)->willReturn(true);
		$this->Localization->Message->expects($this->once())->method('deleteAll')->with(array(1 => 1), false)->willReturn(true);
		$this->Localization->MessageReference->expects($this->once())->method('deleteAll')->with(array(1 => 1), false)->willReturn(true);
		$this->Localization->Translation->expects($this->once())->method('deleteAll')->with(array(1 => 1), false)->willReturn(true);

		$this->Localization->Language->expects($this->once())->method('saveAll')->with($data['Languages'])->willReturn(true);
		$this->Localization->Message->expects($this->once())->method('saveAll')->with($data['Messages'])->willReturn(true);
		$this->Localization->MessageReference->expects($this->once())->method('saveAll')->with($data['References'])->willReturn(true);
		$this->Localization->Translation->expects($this->once())->method('saveAll')->with($data['Translations'])->willReturn(true);

		$this->assertTrue($this->Localization->import($data));
	}

	/**
	 * Data provider for testImport
	 * 
	 * @return array
	 */
	public function importProvider() {
		return array(
			//set #0
			array(
				//data
				array(
					'Export' => array(00),
					'Languages' => array(11),
					'Messages' => array(22),
					'References' => array(33),
					'Translations' => array(44)
				)
			)
		);
	}

	/**
	 * Test export
	 * 
	 * @param array $data
	 * @param array $export
	 * 
	 * @dataProvider exportProvider
	 */
	public function testExport(array $data, array $export) {
		$this->Localization->Language->expects($this->once())->method('find')->with('all')->willReturn($data['Language']);
		$this->Localization->Message->expects($this->once())->method('find')->with('all')->willReturn($data['Message']);
		$this->Localization->MessageReference->expects($this->once())->method('find')->with('all')->willReturn($data['MessageReference']);
		$this->Localization->Translation->expects($this->once())->method('find')->with('all')->willReturn($data['Translation']);

		$exported = $this->Localization->export();
		$this->assertNotEmpty($exported['Export']['created']);
		$this->assertNotEmpty($exported['Export']['created_timestamp']);
		unset($exported['Export']);
		$this->assertSame($export, $exported);
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
				//data
				array(
					'Language' => array(
						array(
							'Language' => array(
								'Language Data'
							)
						)
					),
					'Message' => array(
						array(
							'Message' => array(
								'Message Data'
							)
						)
					),
					'MessageReference' => array(
						array(
							'MessageReference' => array(
								'MessageReference Data'
							)
						)
					),
					'Translation' => array(
						array(
							'Translation' => array(
								'Translation Data'
							)
						)
					),
				),
				//export
				array(
					'Languages' => array(
						array(
							'Language Data'
						)
					),
					'Messages' => array(
						array(
							'Message Data'
						)
					),
					'References' => array(
						array(
							'MessageReference Data'
						)
					),
					'Translations' => array(
						array(
							'Translation Data'
						)
					),
				)
			)
		);
	}

}

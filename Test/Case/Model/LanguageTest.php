<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 13.10.2014
 * Time: 18:03:06
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('Language', 'Localization.Model');

/**
 * LanguageTest
 * 
 * @package LocalizationTest
 * @subpackage Model
 */
class LanguageTest extends CakeTestCase {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $fixtures = array(
		'plugin.Localization.Language'
	);

	/**
	 * Test data validation
	 * 
	 * @param array $data
	 * @param array $validationErrors
	 * 
	 * @dataProvider validationProvider
	 */
	public function testValidation(array $data, array $validationErrors) {
		$Language = ClassRegistry::init('Localization.Language');
		$Language->set($data);
		$this->assertSame(!$validationErrors, $Language->validates());
		$this->assertSame($validationErrors, $Language->validationErrors);
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
					'code' => array(
						'Code must have 3 letters only (lower case)'
					),
					'name' => array(
						'Name must be alphanumeric'
					)
				)
			),
			//set #1
			array(
				//data
				array(
					'code' => 'gh'
				),
				//validationErrors
				array(
					'code' => array(
						'Code must have 3 letters only (lower case)'
					),
					'name' => array(
						'Name must be alphanumeric'
					)
				)
			),
			//set #2
			array(
				//data
				array(
					'code' => 'urd'
				),
				//validationErrors
				array(
					'code' => array(
						'Code must be unique'
					),
					'name' => array(
						'Name must be alphanumeric'
					)
				)
			),
			//set #3
			array(
				//data
				array(
					'code' => 'xxx',
					'name' => str_repeat('x', 101)
				),
				//validationErrors
				array(
					'name' => array(
						'Name length must be less or equal to 100'
					)
				)
			),
			//set #4
			array(
				//data
				array(
					'code' => 'xxx',
					'name' => str_repeat('x', 100)
				),
				//validationErrors
				array()
			),
			//set #5
			array(
				//data
				array(
					'code' => 'xxx',
					'name' => 'English'
				),
				//validationErrors
				array(
					'name' => array(
						'Name must be unique'
					)
				)
			),
		);
	}

}

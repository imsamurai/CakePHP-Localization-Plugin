<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 13.10.2014
 * Time: 14:24:20
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */

/**
 * LocalizationControllerTest
 * 
 * @package LocalizationTest
 * @subpackage Controller
 */
class LocalizationControllerTest extends ControllerTestCase {

	/**
	 * Test export action
	 * 
	 * @param string $output
	 * @param array $headers
	 * 
	 * @dataProvider exportProvider
	 */
	public function testExport($output, array $headers) {
		$Controller = $this->generate('Localization.Localization', array(
			'models' => array(
				'Localization.Localization' => array('export'),
			)
		));

		$Controller->Localization->expects($this->once())->method('export')->willReturn(array(
			'Export' => array(
				'created_timestamp' => 1414143865,
				'created' => '2014-10-24 12:44:25'
			),
			'Languages' => array(),
			'Messages' => array(),
			'References' => array(),
			'Translations' => array()
		));

		$data = $this->testAction('/localization/localization/export', array(
			'method' => 'GET'
		));
		$this->assertStringMatchesFormat($output, $data);
		foreach ($headers as $header => $value) {
			$this->assertStringMatchesFormat($value, $this->headers[$header]);
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
				//output
				'{"Export":{"created_timestamp":1414143865,"created":"2014-10-24 12:44:25"},"Languages":[],"Messages":[],"References":[],"Translations":[]}',
				//headers
				array(
					'Content-Disposition' => 'attachment; filename="localization_export_2014-10-24 12:44:25.json"'
				)
			)
		);
	}

	/**
	 * Test import action
	 * 
	 * @param array $data
	 * @param bool $success
	 * @param string $message
	 * 
	 * @dataProvider importProvider
	 */
	public function testImport($data, $success, $message) {
		$Controller = $this->generate('Localization.Localization', array(
			'models' => array(
				'Localization.Localization' => array('import'),
			),
			'components' => array(
				'Session' => array('setFlash')
			),
			'methods' => array('redirect', 'referer')
		));
		if (!is_null($success)) {
			$Controller->Localization->expects($this->once())->method('import')->with($data)->willReturn($success);
		} else {
			$Controller->Localization->expects($this->never())->method('import');
		}

		$Controller->Session->expects($this->once())->method('setFlash')->with(__($message));

		$Controller
				->expects($this->once())
				->method('referer')
				->willReturn('referer');
		$Controller
				->expects($this->once())
				->method('redirect')
				->with('referer');

		if (!is_null($data)) {
			$tmpName = tempnam('/tmp', 'localization_import_test');
			file_put_contents($tmpName, json_encode($data));
		} else {
			$tmpName = '';
		}

		$this->testAction('/localization/localization/import', array(
			'method' => 'POST',
			'data' => array(
				'Localization' => array(
					'file' => array(
						'tmp_name' => $tmpName
					)
				)
			)
		));
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
				null,
				//success
				null,
				//message
				'Wrong or empty import file!'
			),
			//set #1
			array(
				//data
				array(),
				//success
				null,
				//message
				'Wrong or empty import file!'
			),
			//set #2
			array(
				//data
				array(
					'Export' => array(),
				),
				//success
				null,
				//message
				'Wrong or empty import file!'
			),
			//set #3
			array(
				//data
				array(
					'Export' => array(),
					'Languages' => array(),
				),
				//success
				null,
				//message
				'Wrong or empty import file!'
			),
			//set #4
			array(
				//data
				array(
					'Export' => array(),
					'Languages' => array(),
					'Messages' => array(),
				),
				//success
				null,
				//message
				'Wrong or empty import file!'
			),
			//set #5
			array(
				//data
				array(
					'Export' => array(),
					'Languages' => array(),
					'Messages' => array(),
					'References' => array(),
				),
				//success
				null,
				//message
				'Wrong or empty import file!'
			),
			//set #6
			array(
				//data
				array(
					'Export' => array(),
					'Languages' => array(),
					'Messages' => array(),
					'References' => array(),
					'Translations' => array()
				),
				//success
				false,
				//message
				'Import localization error!'
			),
			//set #7
			array(
				//data
				array(
					'Export' => array(),
					'Languages' => array(),
					'Messages' => array(),
					'References' => array(),
					'Translations' => array()
				),
				//success
				true,
				//message
				'Import localization success!'
			),
		);
	}

}

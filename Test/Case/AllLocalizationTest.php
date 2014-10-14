<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Feb 7, 2014
 * Time: 5:22:47 PM
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */

/**
 * AllLocalizationTest
 * 
 * @package LocalizationTest
 * @subpackage Test
 */
class AllLocalizationTest extends PHPUnit_Framework_TestSuite {

	/**
	 * 	All Monitoring tests suite
	 *
	 * @return PHPUnit_Framework_TestSuite the instance of PHPUnit_Framework_TestSuite
	 */
	public static function suite() {
		$suite = new CakeTestSuite('All Localization Tests');
		$basePath = App::pluginPath('Localization') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($basePath);

		return $suite;
	}

}

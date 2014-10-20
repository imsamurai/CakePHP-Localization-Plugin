<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 17.10.2014
 * Time: 20:08:46
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */
App::uses('LocalizationExtractTask', 'Localization.Console/Command/Task');

/**
 * LocalizationExtractTaskTest
 * 
 * @package LocalizationTest
 * @subpackage Console/Command/Task
 */
class LocalizationExtractTaskTest extends CakeTestCase {

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
		$this->Message = ClassRegistry::init('Localization.Message');
		$this->Message->deleteAll(array(1 => 1));
	}

	/**
	 * Test extract messages
	 */
	public function testExtract() {
		$Task = new LocalizationExtractTask;
		$Task->initialize();
		$Task->params = array(
			'output' => 'db',
			'extract-core' => 'no',
			'plugin' => 'Localization'
		);
		$Task->execute();
		$testMessages = $this->Message->find('all', array(
			'conditions' => array(
				'name LIKE' => '____T%'
			),
			'fields' => array(
				'id',
				'name',
				'js'
			),
			'contain' => array(
				'References'
			)
		));

		$mAlias = $this->Message->alias;
		$rAlias = $this->Message->References->alias;
		$this->assertCount(4, $testMessages);		
		
		$this->assertSame('____TestMessageJS', $testMessages[0][$mAlias]['name']);
		$this->assertCount(1, $testMessages[0][$rAlias]);
		$this->assertSame('message.js', basename($testMessages[0][$rAlias][0]['file']));
		$this->assertSame('0', $testMessages[0][$rAlias][0]['line']);
		$this->assertSame('JS', $testMessages[0][$rAlias][0]['comment']);
		
		$this->assertSame('____TestMessageJS2', $testMessages[1][$mAlias]['name']);
		$this->assertCount(1, $testMessages[0][$rAlias]);
		$this->assertSame('message.js', basename($testMessages[1][$rAlias][0]['file']));
		$this->assertSame('0', $testMessages[1][$rAlias][0]['line']);
		$this->assertSame('JS', $testMessages[1][$rAlias][0]['comment']);
		
		$this->assertSame('____TestMessage', $testMessages[2][$mAlias]['name']);
		$this->assertCount(2, $testMessages[2][$rAlias]);
		$this->assertSame('message.js', basename($testMessages[2][$rAlias][0]['file']));
		$this->assertSame('0', $testMessages[2][$rAlias][0]['line']);
		$this->assertSame('JS', $testMessages[2][$rAlias][0]['comment']);
		$this->assertSame('message.php', basename($testMessages[2][$rAlias][1]['file']));
		$this->assertSame('6', $testMessages[2][$rAlias][1]['line']);
		
		$this->assertSame('____TestMessage2', $testMessages[3][$mAlias]['name']);
		$this->assertCount(2, $testMessages[3][$rAlias]);
		$this->assertSame('message.php', basename($testMessages[3][$rAlias][0]['file']));
		$this->assertSame('message.php', basename($testMessages[3][$rAlias][1]['file']));
		$this->assertSame('7', $testMessages[3][$rAlias][0]['line']);
		$this->assertSame('8', $testMessages[3][$rAlias][1]['line']);
	}

}

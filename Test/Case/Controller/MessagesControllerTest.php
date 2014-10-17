<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 13.10.2014
 * Time: 17:18:04
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */

/**
 * MessagesControllerTest
 * 
 * @package LocalizationTest
 * @subpackage Controller
 */
class MessagesControllerTest extends ControllerTestCase {

	/**
	 * {@inheritdoc}
	 */
	public function setUp() {
		parent::setUp();
		Configure::write('Pagination.limit', 10);
	}

	/**
	 * Test index action
	 * 
	 * @param array $query
	 * @param array $paginate
	 * @dataProvider indexProvider
	 */
	public function testIndex(array $query, array $paginate) {
		$Controller = $this->generate('Localization.Messages', array(
			'models' => array(
				'Localization.Language',
				'Localization.MessageReference',
			),
			'methods' => array(
				'paginate',
				'beforeRender'
			)
		));

		$Controller->expects($this->once())->method('paginate')->with('Message')->willReturn(array('Message pagination data'));
		$Controller->Message->Translations->tablePrefix = 'x_';
		$Controller->Message->Translations->table = 'trans';
		$Controller->Message->alias = 'Mes';

		if (!empty($query['file'])) {
			$Controller->MessageReference->expects($this->once())->method('find')
					->with('list', array(
						'fields' => array('message_id', 'message_id'),
						'conditions' => array(
							'LOWER(file) LIKE' => "%" . mb_strtolower($query['file']) . "%"
						)
					))
					->willReturn(array(1, 2, 3));
		}

		$this->testAction('/localization/messages/index', array(
			'method' => 'GET',
			'data' => $query
		));

		$this->assertEqual($paginate, $Controller->paginate);
		$this->assertSame(array('Message pagination data'), $Controller->viewVars['data']);
	}

	/**
	 * Data provider for testIndex
	 * 
	 * @return array
	 */
	public function indexProvider() {
		return array(
			//set #0
			array(
				//query
				array(
					'id' => '1'
				),
				//paginate
				array(
					'Message' => array(
						'limit' => 10,
						'fields' => array(
							'id',
							'name',
							'js',
							'created',
							'modified'
						),
						'conditions' => array(
							'id' => '1'
						),
						'order' => array('modified' => 'desc'),
						'contain' => array(
							'Translations' => array(
								'fields' => array(
									'language_id',
									'translated'
								)
							),
							'References'
						)
					)
				)
			),
			//set #1
			array(
				//query
				array(
					'id' => '1',
					'created' => '12:00:01 01.01.2014 to 01:00:10 02.03.2014',
					'modified' => '2014/01/01 11:05:23 - 2014/02/02 17:05:23',
					'name' => 'sOme',
					'not_translated_language_id' => array('1', '2', '4')
				),
				//paginate
				array(
					'Message' => array(
						'limit' => 10,
						'fields' => array(
							'id',
							'name',
							'js',
							'created',
							'modified'
						),
						'conditions' => array(
							'id' => '1',
							'created BETWEEN ? AND ?' => array('2014-01-01 12:00:01', '2014-03-02 01:00:10'),
							'modified BETWEEN ? AND ?' => array('2014-01-01 11:05:23', '2014-02-02 17:05:23'),
							'LOWER(name) LIKE' => '%some%',
							'(SELECT count(*) from x_trans WHERE message_id=Mes.id AND language_id IN (1,2,4) AND text!="") <' => 3
						),
						'order' => array('modified' => 'desc'),
						'contain' => array(
							'Translations' => array(
								'fields' => array(
									'language_id',
									'translated'
								)
							),
							'References'
						)
					)
				)
			),
			//set #2
			array(
				//query
				array(
					'not_translated_language_id' => array('1', '2', '4'),
					'file' => 'home.ctp'
				),
				//paginate
				array(
					'Message' => array(
						'limit' => 10,
						'fields' => array(
							'id',
							'name',
							'js',
							'created',
							'modified'
						),
						'conditions' => array(
							'(SELECT count(*) from x_trans WHERE message_id=Mes.id AND language_id IN (1,2,4) AND text!="") <' => 3,
							'id' => array(1, 2, 3)
						),
						'order' => array('modified' => 'desc'),
						'contain' => array(
							'Translations' => array(
								'fields' => array(
									'language_id',
									'translated'
								)
							),
							'References'
						)
					)
				)
			),
		);
	}

	/**
	 * Test create action
	 * 
	 * @param array $query
	 * @param bool $saved
	 * @param bool $success
	 * @param string $message
	 * 
	 * @dataProvider createProvider
	 */
	public function testCreate(array $query, $saved, $success, $message) {
		$Controller = $this->generate('Localization.Messages', array(
			'models' => array(
				'Localization.Message' => array('saveAssociatedChanged'),
			),
			'components' => array(
				'Session' => array('setFlash'),
			),
			'methods' => array(
				'render'
			)
		));

		if ($saved) {
			$id = 123;
			if ($success) {
				$Controller->expects($this->once())->method('render')->with('edit');
			}
			$Controller->Message->expects($this->once())->method('saveAssociatedChanged')->with($query)
					->willReturnCallback(function() use ($success, $id, $Controller) {
						$Controller->Message->id = $id;
						return $success;
					});
			$Controller->Session->expects($this->once())->method('setFlash')->with($this->matches($message));
		}

		$this->testAction('/localization/messages/create', array(
			'method' => 'POST',
			'data' => $query
		));

		if ($saved) {
			$this->assertSame($id, $Controller->viewVars['id']);
		}
	}

	/**
	 * Data provider for testCreate
	 * 
	 * @return array
	 */
	public function createProvider() {
		return array(
			//set #0
			array(
				//query
				array(),
				//saved
				false,
				//success
				null,
				//message
				''
			),
			//set #1
			array(
				//query
				array(
					'Message' => array(
						'name' => 'message1',
						'js' => true
					),
					'Translations' => array(
						array(
							'language_id' => '1',
							'text' => 'translated message'
						)
					)
				),
				//saved
				true,
				//success
				true,
				//message
				'Message created. <a href="%s">Create new</a> or <a href="%s">view all</a>'
			),
			//set #2
			array(
				//query
				array(
					'code' => 'ukr',
					'name' => 'Ukrainian'
				),
				//saved
				true,
				//success
				false,
				//message
				'Can\'t create message! You can <a href="%s">view all</a>'
			),
		);
	}

	/**
	 * Test edit action
	 * 
	 * @param int $id
	 * @param array $query
	 * @param array $data
	 * @param bool $success
	 * @param string $message
	 * @param string $exception
	 * 
	 * @dataProvider editProvider
	 */
	public function testEdit($id, array $query, $data, $success, $message, $exception) {
		$Controller = $this->generate('Localization.Messages', array(
			'models' => array(
				'Localization.Message' => array('saveAssociatedChanged', 'getById'),
			),
			'components' => array(
				'Session' => array('setFlash'),
			)
		));

		if ($exception) {
			$this->expectException($exception);
		}

		if (!empty($query)) {
			$Controller->Message->expects($this->once())->method('saveAssociatedChanged')->with($query)
					->willReturnCallback(function() use ($success, $id, $Controller) {
						$Controller->Message->id = $id;
						return $success;
					});
			$Controller->Session->expects($this->once())->method('setFlash')->with($this->matches($message));
		}

		if (empty($query)) {
			$Controller->Message->expects($this->once())->method('getById')->with($id)->willReturn($data);
		}

		$this->testAction('/localization/message/edit/' . $id, array(
			'method' => 'POST',
			'data' => $query
		));

		if ($success) {
			$this->assertSame($id, $Controller->viewVars['id']);
		}

		if (!empty($data)) {
			$this->assertSame($data, $Controller->request->data);
		}
	}

	/**
	 * Data provider for testEdit
	 * 
	 * @return array
	 */
	public function editProvider() {
		return array(
			//set #0
			array(
				//id
				'1',
				//query
				array(),
				//data
				array(
					'Message' => array(
						'name' => 'message1',
						'js' => true
					),
					'Translations' => array(
						array(
							'language_id' => '1',
							'text' => 'translated message'
						)
					)
				),
				//success
				null,
				//message
				'',
				//exception
				'',
			),
			//set #1
			array(
				//id
				'1',
				//query
				array(),
				//data
				array(),
				//success
				null,
				//message
				'',
				//exception
				'NotFoundException',
			),
			//set #2
			array(
				//id
				'2',
				//query
				array(
					'Message' => array(
						'name' => 'message1',
						'js' => true
					),
					'Translations' => array(
						array(
							'language_id' => '1',
							'text' => 'translated message'
						)
					)
				),
				//data
				null,
				//success
				true,
				//message
				'Message saved. <a href="%s">Create new</a> or <a href="%s">view all</a>',
				//exception
				''
			),
			//set #3
			array(
				//id
				'2',
				//query
				array(
					'Message' => array(
						'name' => 'message1',
						'js' => true
					),
					'Translations' => array(
						array(
							'language_id' => '1',
							'text' => 'translated message'
						)
					)
				),
				//data
				null,
				//success
				false,
				//message
				'Can\'t save message! You can <a href="%s">view all</a>',
				//exception
				''
			),
		);
	}

	/**
	 *  Test export action
	 * 
	 * @param bool $success
	 * @param string $message
	 * 
	 * @dataProvider exportProvider
	 */
	public function testExport($success, $message) {
		$Controller = $this->generate('Localization.Messages', array(
			'models' => array(
				'Localization.Message' => array('export'),
			),
			'components' => array(
				'Session' => array('setFlash'),
			),
			'methods' => array('redirect', 'referer')
		));
		$Controller
				->expects($this->once())
				->method('referer')
				->willReturn('referer');
		$Controller
				->expects($this->once())
				->method('redirect')
				->with('referer');
		$Controller->Message->expects($this->once())->method('export')->willReturn($success);
		$Controller->Session->expects($this->once())->method('setFlash')->with($this->matches($message));

		$this->testAction('/localization/message/export/', array(
			'method' => 'GET'
		));
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
				//success
				true,
				//message
				'Messages exported successfully!'
			),
			//set #1
			array(
				//success
				false,
				//message
				'Can\'t export messages!'
			),
		);
	}

	/**
	 * Test delete action
	 * 
	 * @param int $id
	 * @param bool $success
	 * @param string $message
	 * @param string $exception
	 * 
	 * @dataProvider deleteProvider
	 */
	public function testDelete($id, $success, $message, $exception) {
		$Controller = $this->generate('Localization.Messages', array(
			'models' => array(
				'Localization.Message' => array('delete', 'findById'),
			),
			'components' => array(
				'Session' => array('setFlash'),
			),
			'methods' => array('redirect', 'referer')
		));

		if ($exception) {
			$this->expectException($exception);
		} else {
			$Controller->Message
					->expects($this->once())
					->method('findById')
					->with($id)
					->willReturn(!$exception);

			$Controller->Message
					->expects($this->once())
					->method('delete')
					->with($id)
					->willReturn($success);

			$Controller->Session
					->expects($this->once())
					->method('setFlash')
					->with($this->matches($message));
			$Controller
					->expects($this->once())
					->method('referer')
					->willReturn('referer');
			$Controller
					->expects($this->once())
					->method('redirect')
					->with('referer');
		}

		$this->testAction('/localization/messages/delete/' . $id, array(
			'method' => 'GET'
		));
	}

	/**
	 * Data provider for testDelete
	 * 
	 * @return array
	 */
	public function deleteProvider() {
		return array(
			//set #0
			array(
				//id
				'1',
				//success
				false,
				//message
				'',
				//exception
				'NotFoundException'
			),
			//set #1
			array(
				//id
				'2',
				//success
				false,
				//message
				'Can\'t delete message #2!',
				//exception
				''
			),
			//set #2
			array(
				//id
				'23',
				//success
				true,
				//message
				'Message #23 deleted',
				//exception
				''
			),
		);
	}

	/**
	 * Test beforeRender callback
	 */
	public function testBeforeRender() {
		$Controller = $this->generate('Localization.Messages', array(
			'models' => array(
				'Localization.Language' => array('find'),
			)
		));
		$Controller->Language
				->expects($this->once())
				->method('find')
				->with('list', array('values' => array('id', 'name')))
				->willReturn(array('Languages list'));
		$Controller->beforeRender();
		$this->assertSame(array('Languages list'), $Controller->viewVars['languages']);
	}

}

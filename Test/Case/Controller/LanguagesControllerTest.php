<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 13.10.2014
 * Time: 14:24:20
 * Format: http://book.cakephp.org/2.0/en/development/testing.html
 */

/**
 * LanguagesControllerTest
 * 
 * @package LocalizationTest
 * @subpackage Controller
 */
class LanguagesControllerTest extends ControllerTestCase {

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
		$Controller = $this->generate('Localization.Languages', array(
			'models' => array(
				'Localization.Language' => array('find'),
			),
			'methods' => array(
				'paginate'
			)
		));

		$Controller->expects($this->once())->method('paginate')->with('Language')->willReturn(array('Language pagination data'));

		$this->testAction('/localization/language/index', array(
			'method' => 'GET',
			'data' => $query
		));

		$this->assertEqual($paginate, $Controller->paginate);
		$generatedQuery = $Controller->request->query;
		unset($generatedQuery['url']);
		$this->assertSame($query, $generatedQuery);
		$this->assertSame(array('Language pagination data'), $Controller->viewVars['data']);
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
					'Language' => array(
						'limit' => 10,
						'fields' => array(
							'id',
							'code',
							'name',
							'created',
							'modified',
						),
						'conditions' => array(
							'id' => '1'
						),
						'order' => array('modified' => 'desc')
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
		$Controller = $this->generate('Localization.Languages', array(
			'models' => array(
				'Localization.Language' => array('save'),
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
			$Controller->Language->expects($this->once())->method('save')->with($query, true, array('code', 'name'))
					->willReturnCallback(function() use ($success, $id, $Controller) {
						$Controller->Language->id = $id;
						return $success;
					});
			$Controller->Session->expects($this->once())->method('setFlash')->with($this->matches($message));
		}

		$this->testAction('/localization/language/create', array(
			'method' => 'POST',
			'data' => array('Language' => $query)
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
					'code' => 'ukr',
					'name' => 'Ukrainian'
				),
				//saved
				true,
				//success
				true,
				//message
				'Language created. <a href="%s">Create new</a> or <a href="%s">view all</a>'
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
				'Can\'t create language! You can <a href="%s">view all</a>'
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
		$Controller = $this->generate('Localization.Languages', array(
			'models' => array(
				'Localization.Language' => array('save', 'read'),
			),
			'components' => array(
				'Session' => array('setFlash'),
			)
		));

		if ($exception) {
			$this->expectException($exception);
		}

		if (!empty($query)) {
			$Controller->Language->expects($this->once())->method('save')->with($query['Language'], true, array('code', 'name'))
					->willReturnCallback(function() use ($success, $id, $Controller) {
						$Controller->Language->id = $id;
						return $success;
					});
			$Controller->Session->expects($this->once())->method('setFlash')->with($this->matches($message));
		}

		if (empty($query)) {
			$Controller->Language->expects($this->once())->method('read')->with(null, $id)->willReturn($data);
		}

		$this->testAction('/localization/language/edit/' . $id, array(
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
					'Language' => array(
						'code' => 'ukr',
						'name' => 'Ukrainian'
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
					'Language' => array(
						'code' => 'ukr',
						'name' => 'Ukrainian'
					)
				),
				//data
				null,
				//success
				true,
				//message
				'Language saved. <a href="%s">Create new</a> or <a href="%s">view all</a>',
				//exception
				''
			),
			//set #3
			array(
				//id
				'2',
				//query
				array(
					'Language' => array(
						'code' => 'ukr',
						'name' => 'Ukrainian'
					)
				),
				//data
				null,
				//success
				false,
				//message
				'Can\'t save language! You can <a href="%s">view all</a>',
				//exception
				''
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
		$Controller = $this->generate('Localization.Languages', array(
			'models' => array(
				'Localization.Language' => array('delete', 'findById'),
			),
			'components' => array(
				'Session' => array('setFlash'),
			),
			'methods' => array('redirect', 'referer')
		));

		if ($exception) {
			$this->expectException($exception);
		} else {
			$Controller->Language
					->expects($this->once())
					->method('findById')
					->with($id)
					->willReturn(!$exception);

			$Controller->Language
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

		$this->testAction('/localization/language/delete/' . $id, array(
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
				'Can\'t delete language #2!',
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
				'Language #23 deleted',
				//exception
				''
			),
		);
	}

}

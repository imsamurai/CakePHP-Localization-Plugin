<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:27:22 PM
 * Format: http://book.cakephp.org/2.0/en/controllers.html
 */
App::uses('LocalizationAppController', 'Localization.Controller');

/**
 * Localization Language Controller
 * 
 * @property Language $Language Language Model
 * 
 * @package Localization
 * @subpackage Controller
 */
class LanguagesController extends LocalizationAppController {

	/**
	 * List of all languages
	 */
	public function index() {
		$this->request->data('Language', $this->request->query);
		$conditions = $this->request->query;
		unset($conditions['url']);
		$this->paginate = array(
			'Language' => array(
				'limit' => Configure::read('Pagination.limit'),
				'fields' => array(
					'id',
					'code',
					'name',
					'created',
					'modified',
				),
				'conditions' => $conditions,
				'order' => array('modified' => 'desc')
			)
		);
		$this->set(array(
			'data' => $this->paginate("Language")
		));
	}

	/**
	 * Create new language
	 */
	public function create() {
		if ($this->_save()) {
			$this->render('edit');
		}
	}

	/**
	 * Edit existing language
	 * 
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function edit($id) {
		$this->_save($id);
		if (!$this->request->data) {
			$data = $this->Language->read(null, $id);
			if (!$data) {
				throw new NotFoundException('Language not found!');
			}
			$this->request->data = $data;
		}

		$this->set('id', $id);
	}

	/**
	 * Delete language
	 * 
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function delete($id) {
		if (!$this->Language->findById($id)) {
			throw new NotFoundException("Language #$id does not exists!");
		}
		$success = $this->Language->delete($id);
		if ($success) {
			$this->Session->setFlash("Language #$id deleted", 'alert/simple', array(
				'class' => 'alert-success', 'title' => 'Ok!'
			));
		} else {
			$this->Session->setFlash("Can't delete language #$id!", 'alert/simple', array(
				'class' => 'alert-error', 'title' => 'Error!'
			));
		}

		$this->redirect($this->referer());
	}

	/**
	 * Save/create language
	 * 
	 * @param int $id
	 * @return bool Trune on success
	 */
	protected function _save($id = null) {
		$data = $this->request->data('Language');
		if (!$data) {
			return false;
		}
		$createUrl = Router::url(array('action' => 'create'));
		$listUrl = Router::url(array('action' => 'index'));
		$this->Language->id = $id;
		$success = $this->Language->save($data, true, array('code', 'name'));
		if ($success) {
			$this->Session->setFlash("Language " . ($id ? 'saved' : 'created') . ". <a href=\"$createUrl\">Create new</a> or <a href=\"$listUrl\">view all</a>", 'alert/simple', array(
				'class' => 'alert-success', 'title' => 'Ok!'
			));
		} else {
			$this->Session->setFlash("Can't " . ($id ? 'save' : 'create') . " language! You can <a href=\"$listUrl\">view all</a>", 'alert/simple', array(
				'class' => 'alert-error', 'title' => 'Error!'
			));
		}
		$this->set('id', $this->Language->id);
		return $success;
	}

}

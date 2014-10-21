<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:27:22 PM
 * Format: http://book.cakephp.org/2.0/en/controllers.html
 */
App::uses('LocalizationAppController', 'Localization.Controller');

/**
 * MessagesController
 * 
 * @property Message $Message Message model
 * @property Language $Language Language model
 * @property MessageReference $MessageReference Message Reference model
 * 
 * @package Localization
 * @subpackage Controller
 */
class MessagesController extends LocalizationAppController {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $uses = array('Localization.Message', 'Localization.Language', 'Localization.MessageReference');

	/**
	 * List of messages
	 */
	public function index() {
		$this->request->data('Message', $this->request->query);
		$this->paginate = array(
			'Message' => array(
				'limit' => Configure::read('Pagination.limit'),
				'fields' => array(
					'id',
					'name',
					'js',
					'created',
					'modified'
				),
				'conditions' => $this->_paginationFilter(),
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
		);
		$this->set(array(
			'data' => $this->paginate("Message")
		));
	}

	/**
	 * Create new message
	 */
	public function create() {
		if ($this->_save()) {
			$this->render('edit');
		}
	}

	/**
	 * Edit message
	 * 
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function edit($id) {
		$this->_save($id);
		if (!$this->request->data) {
			$data = $this->Message->getById($id);
			if (!$data) {
				throw new NotFoundException(__("Message #%s does not exists!", $id));
			}
			$this->request->data = $data;
		}

		$this->set('id', $id);
	}

	/**
	 * {@inheritdoc}
	 */
	public function beforeRender() {
		$this->set(array(
			'languages' => $this->Language->find('list', array('values' => array('id', 'name')))
		));
	}

	/**
	 * Export messages to files
	 */
	public function export() {
		if ($this->Message->export()) {
			$this->Session->setFlash(__("Messages exported successfully!"), 'alert/simple', array(
				'class' => 'alert-success', 'title' => __('Ok!')
			));
		} else {
			$this->Session->setFlash(__("Can't export messages!"), 'alert/simple', array(
				'class' => 'alert-error', 'title' => __('Error!')
			));
		}
		$this->redirect($this->referer());
	}

	/**
	 * Delete language
	 * 
	 * @param int $id
	 * @throws NotFoundException
	 */
	public function delete($id) {
		if (!$this->Message->findById($id)) {
			throw new NotFoundException(__("Message #%s does not exists!", $id));
		}
		$success = $this->Message->delete($id);
		if ($success) {
			$this->Session->setFlash(__("Message #%s deleted", $id), 'alert/simple', array(
				'class' => 'alert-success', 'title' => __('Ok!')
			));
		} else {
			$this->Session->setFlash(__("Can't delete message #%s!", $id), 'alert/simple', array(
				'class' => 'alert-error', 'title' => __('Error!')
			));
		}

		$this->redirect($this->referer());
	}

	/**
	 * Save/create message
	 * 
	 * @param int $id
	 * @return bool True on success
	 */
	protected function _save($id = null) {
		$data = $this->request->data;
		if (!$data) {
			return false;
		}
		$createUrl = Router::url(array('action' => 'create'));
		$listUrl = Router::url(array('action' => 'index'));
		$this->Message->id = $id;
		$success = $this->Message->saveAssociatedChanged($data);
		if ($success) {
			$this->Session->setFlash(__("Message %s. <a href=\"%s\">Create new</a> or <a href=\"%s\">view all</a>", __($id ? 'saved' : 'created'), $createUrl, $listUrl), 'alert/simple', array(
				'class' => 'alert-success', 'title' => __('Ok!')
			));
			$this->redirect(array('action' => 'edit', $this->Message->id));
		} else {
			$this->Session->setFlash(__("Can't %s message! You can <a href=\"%s\">view all</a>",  __($id ? 'save' : 'create'), $listUrl), 'alert/simple', array(
				'class' => 'alert-error', 'title' => __('Error!')
			));
		}
		$this->set('id', $this->Message->id);
		return $success;
	}

	/**
	 * Builds pagination conditions from search form
	 * 
	 * @return array
	 */
	protected function _paginationFilter() {
		$conditions = array_filter($this->request->query, function($var) {
			return $var !== '';
		});
		unset($conditions['url']);
		foreach (array('modified', 'created') as $dateRangeField) {
			if (empty($conditions[$dateRangeField])) {
				continue;
			}
			if (preg_match('/^(?P<start>.*)\s(-|to)\s(?P<end>.*)$/is', $conditions[$dateRangeField], $range)) {
				$conditions[$dateRangeField . ' BETWEEN ? AND ?'] = array(
					(new DateTime($range['start']))->format('Y-m-d H:i:s'),
					(new DateTime($range['end']))->format('Y-m-d H:i:s')
				);
			}
			unset($conditions[$dateRangeField]);
		}

		if (!empty($conditions['not_translated_language_id'])) {
			$notTranslatedQuery = '(SELECT count(*) from ' .
					$this->Message->Translations->tablePrefix .
					$this->Message->Translations->table .
					' WHERE message_id=' .
					$this->Message->alias .
					'.id AND language_id IN (' .
					implode(',', array_map('intval', $conditions['not_translated_language_id'])) .
					') AND text!="")';
			$conditions["$notTranslatedQuery <"] = count($conditions['not_translated_language_id']);
		}
		unset($conditions['not_translated_language_id']);

		if (!empty($conditions['name'])) {
			$conditions['LOWER(name) LIKE'] = "%" . mb_strtolower($conditions['name']) . "%";
		}
		unset($conditions['name']);
		
		if (!empty($conditions['file'])) {
			$ids = $this->MessageReference->find('list', array(
				'fields' => array('message_id', 'message_id'),
				'conditions' => array(
					'LOWER(file) LIKE' => "%" . mb_strtolower($conditions['file']) . "%"
				)
			));
			$conditions['id'] = array_values($ids);
		}
		unset($conditions['file']);

		return $conditions;
	}

}

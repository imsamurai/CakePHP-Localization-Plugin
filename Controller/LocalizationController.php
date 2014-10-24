<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:27:22 PM
 * Format: http://book.cakephp.org/2.0/en/controllers.html
 */
App::uses('LocalizationAppController', 'Localization.Controller');

/**
 * LocalizationController
 * 
 * @property Localization $Localization Localization model
 * 
 * @package Localization
 * @subpackage Controller
 */
class LocalizationController extends LocalizationAppController {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $uses = array(
		'Localization.Localization'
	);

	/**
	 * Localization plugin main page
	 */
	public function index() {
	}

	/**
	 * Export all data into file
	 */
	public function export() {
		$data = $this->Localization->export();
		$this->response->body(json_encode($data));
		$this->response->type('json');
		$this->response->download('localization_export_' . $data['Export']['created'] . '.json');
		return $this->response;
	}

	/**
	 * Import data from file
	 */
	public function import() {
		$fileName = $this->request->data('Localization.file.tmp_name');
		$data = $fileName ? json_decode(file_get_contents($fileName), true) : null;
		if (!$data || !isset($data['Languages']) || !isset($data['Messages']) || !isset($data['Translations']) || !isset($data['References'])) {
			$this->Session->setFlash(__("Wrong or empty import file!"), 'alert/simple', array(
				'class' => 'alert-error', 'title' => __('Error!')
			));
			return $this->redirect($this->referer());
		}

		$success = $this->Localization->import($data);
		if ($success) {
			$this->Session->setFlash(__("Import localization success!"), 'alert/simple', array(
				'class' => 'alert-success', 'title' => __('Ok!')
			));
		} else {
			$this->Session->setFlash(__("Import localization error!"), 'alert/simple', array(
				'class' => 'alert-error', 'title' => __('Error!')
			));
		}
		$this->redirect($this->referer());
	}

}

<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:36:02 PM
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('LocalizationAppModel', 'Localization.Model');

/**
 * Localization Model
 * 
 * @property Language $Language Localization language model
 * @property Message $Message Localization message model
 * @property MessageReference $MessageReference Localization message reference model
 * @property Translation $Translation Localization translation model
 * 
 * @package Localization
 * @subpackage Model
 */
class Localization extends LocalizationAppModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $useTable = false;

	/**
	 * {@inheritdoc}
	 * 
	 * @param bool|int|string|array $id Set this ID for this model on startup,
	 *   can also be an array of options, see above.
	 * @param string $table Name of database table to use.
	 * @param string $ds DataSource connection name.
	 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->Language = ClassRegistry::init('Localization.Language');
		$this->Message = ClassRegistry::init('Localization.Message');
		$this->MessageReference = ClassRegistry::init('Localization.MessageReference');
		$this->Translation = ClassRegistry::init('Localization.Translation');
	}

	/**
	 * Export data
	 * 
	 * @return array
	 */
	public function export() {
		$Created = new DateTime();
		return array(
			'Export' => array(
				'created_timestamp' => $Created->getTimestamp(),
				'created' => $Created->format('Y-m-d H:i:s')
			),
			'Languages' => Hash::extract((array)$this->Language->find('all'), '{n}.{s}'),
			'Messages' => Hash::extract((array)$this->Message->find('all'), '{n}.{s}'),
			'References' => Hash::extract((array)$this->MessageReference->find('all'), '{n}.{s}'),
			'Translations' => Hash::extract((array)$this->Translation->find('all'), '{n}.{s}')
		);
	}

	/**
	 * Import data
	 * 
	 * @param array $data
	 * @return bool
	 */
	public function import(array $data) {
		$success = $this->clearAll();
		$success = (bool)$this->Language->saveAll($data['Languages']) && $success;
		$success = (bool)$this->Message->saveAll($data['Messages']) && $success;
		$success = (bool)$this->MessageReference->saveAll($data['References']) && $success;
		$success = (bool)$this->Translation->saveAll($data['Translations']) && $success;
		return $success;
	}

	/**
	 * Delete all localization records
	 * 
	 * @return bool
	 */
	public function clearAll() {
		$success = true;
		$success = (bool)$this->Language->deleteAll(array(1 => 1), false) && $success;
		$success = (bool)$this->Message->deleteAll(array(1 => 1), false) && $success;
		$success = (bool)$this->MessageReference->deleteAll(array(1 => 1), false) && $success;
		$success = (bool)$this->Translation->deleteAll(array(1 => 1), false) && $success;
		return $success;
	}

}

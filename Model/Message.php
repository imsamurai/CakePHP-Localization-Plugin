<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: Jan 14, 2014
 * Time: 6:36:02 PM
 * Format: http://book.cakephp.org/2.0/en/models.html
 */
App::uses('LocalizationAppModel', 'Localization.Model');

/**
 * Localization Message Model
 * 
 * @property Translation $Translations Translation model
 * @property MessageReference $References Message Reference model
 * 
 * @package Localization
 * @subpackage Model
 */
class Message extends LocalizationAppModel {

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $validate = array(
		'name' => array(
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Name must be unique',
			),
			'minLength' => array(
				'rule' => array('minLength', 1),
				'required' => true,
				'allowEmpty' => false,
				'message' => 'Name should not be empty',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 200),
				'message' => 'Name length must be less or equal to 200'
			),
		)
	);

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Translations' => array(
			'className' => 'Localization.Translation',
			'dependent' => true
		),
		'References' => array(
			'className' => 'Localization.MessageReference',
			'dependent' => true
		),
	);

	/**
	 * Save associated (only changed/new associated records)
	 * 
	 * @param array $data
	 * @param array $options
	 * @return mixed If atomic: True on success, or false on failure.
	 *    Otherwise: array similar to the $data array passed, but values are set to true/false
	 *    depending on whether each record saved successfully.
	 */
	public function saveAssociatedChanged(array $data, array $options = array()) {
		$this->set($data);
		if ($this->id) {
			$original = $this->getById($this->id);

			if (!empty($data['Translations'])) {
				foreach ($original['Translations'] as $languageId => $translation) {
					if ($translation['text'] === $data['Translations'][$languageId]['text']) {
						unset($data['Translations'][$languageId]);
					}
				}
			}

			if (!empty($data['References'])) {
				$this->References->deleteAll(array(
					'message_id' => $this->id
						), false);
			}
		}
		return $this->saveAssociated($data, $options);
	}

	/**
	 * Return message with translations linked by language ids
	 * 
	 * @param int $id
	 * @return array
	 */
	public function getById($id) {
		$data = $this->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => array(
				'Translations',
				'References'
			)
		));
		if ($data) {
			$data['Translations'] = Hash::combine($data['Translations'], '{n}.language_id', '{n}');
		}
		return $data;
	}

	/**
	 * Export all messages to files
	 * 
	 * @return bool True on success
	 */
	public function export() {
		$languages = $this->Translations->Language->find('all', array(
			'contain' => array(
				'Translations' => array(
					'Message',
					'conditions' => array(
						'text !=' => ''
					)
				)
			)
		));

		$success = true;
		foreach ($languages as $language) {
			$code = $language['Language']['code'];
			$data = array(
				'php' => array(),
				'js' => array()
			);
			foreach ($language['Translations'] as $translation) {
				if ((bool)$translation['Message']['js']) {
					$data['js'][$translation['Message']['name']] = $translation['text'];
				}
				$data['php'][$translation['Message']['name']] = $translation['text'];
			}
			$success = $success && $this->_exportLocale($code, $data['php']) && $this->_exportJsLocale($code, $data['js']);
		}
		return $success;
	}

	/**
	 * Save php localization into file
	 * 
	 * @param string $code Language code
	 * @param array $data
	 * @return bool True on succes
	 */
	protected function _exportLocale($code, array $data) {
		$path = sprintf(Configure::read('Localization.path'), $code);
		//@codingStandardsIgnoreStart
		@mkdir($path, 0777, true);
		//@codingStandardsIgnoreEnd
		$LocaleFile = new File($path . 'default.po', true);
		$success = $LocaleFile->write(Configure::read('Localization.header'));
		foreach ($data as $msgid => $msgstr) {
			$success = $success && $LocaleFile->append("msgid \"" . $msgid . "\"\n");
			$success = $success && $LocaleFile->append($this->_buildMsgstr($msgstr) . "\n");
		}
		$LocaleFile->close();
		return $success;
	}

	/**
	 * Save js localization into file
	 * 
	 * @param string $code Language code
	 * @param array $data
	 * @return bool True on succes
	 */
	protected function _exportJsLocale($code, array $data) {
		$path = sprintf(Configure::read('Localization.jsPath'), $code);
		//@codingStandardsIgnoreStart
		@mkdir($path, 0777, true);
		//@codingStandardsIgnoreEnd
		$LocaleFile = new File($path . 'default.js', true);
		$success = $LocaleFile->write(sprintf(Configure::read('Localization.jsTemplate'), json_encode($data)));
		$LocaleFile->close();
		return $success;
	}

	/**
	 * Build message string for po file
	 * 
	 * @param string $message
	 * @param int $limit
	 * @return string
	 */
	protected function _buildMsgstr($message, $limit = 500) {
		$output = '';
		if (mb_strlen($message) > $limit) {
			$chunks = ceil(mb_strlen($message) / $limit);
			$output = "msgstr \"\"\n";
			for ($chunk = 0; $chunk < $chunks; $chunk++) {
				$output .= "\"" . mb_substr($message, $chunk * $limit, $limit) . "\"\n";
			}
		} else {
			$output = "msgstr \"" . $message . "\"\n";
		}
		return $output;
	}

}

<?php

/**
 * Author: imsamurai <im.samuray@gmail.com>
 * Date: 17.10.2014
 * Time: 14:21:20
 * Format: http://book.cakephp.org/2.0/en/console-and-shells.html#shell-tasks
 */
App::uses('ExtractTask', 'Console/Command/Task');

/**
 * LocalizationExtractTask
 * 
 * @property Message $Message Localization Message model
 * 
 * @package Localization
 * @subpackage Console.Command.Task
 */
class LocalizationExtractTask extends ExtractTask {

	/**
	 * {@inheritdoc}
	 *
	 * @var string
	 */
	public $name = 'Extract';

	/**
	 * {@inheritdoc}
	 *
	 * @var array
	 */
	public $uses = array(
		'Localization.Message'
	);

	/**
	 * Allowed file types
	 */
	public $fileTypes = 'php|ctp|thtml|inc|tpl|js';

	/**
	 * {@inheritdoc}
	 * 
	 * @return ConsoleOptionParser
	 */
	public function getOptionParser() {
		return parent::getOptionParser()
						->description('Language String Extraction')
						->addOption('output', array(
							'help' => __d('cake_console', 'Full path to output directory. Or `db` for database.')
		));
	}

	/**
	 *  {@inheritdoc}
	 */
	public function execute() {
		new PHP_CodeSniffer;
		if (isset($this->params['output']) && $this->params['output'] === 'db') {
			$this->params['merge'] = 'yes';
		}
		if (isset($this->params['paths']) && ($this->params['paths'] == 'app')) {
			$this->params['paths'] = APP;
		}
		parent::execute();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _extract() {
		if (rtrim($this->_output, DS) !== 'db') {
			return parent::_extract();
		}

		$this->out();
		$this->out();
		$this->out(__d('cake_console', 'Extracting...'));
		$this->hr();
		$this->_extractTokens();
		$this->_extractValidationMessages();
		$this->_writeDb();
		$this->_paths = $this->_files = $this->_storage = array();
		$this->_translations = $this->_tokens = array();
		$this->_extractValidation = true;
		$this->out();
		$this->out(__d('cake_console', 'Done.'));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _searchFiles() {
		$this->_hhvmFix();
		$pattern = false;
		if (!empty($this->_exclude)) {
			$exclude = array();
			foreach ($this->_exclude as $e) {
				if (DS !== '\\' && $e[0] !== DS) {
					$e = DS . $e;
				}
				$exclude[] = preg_quote($e, '/');
			}
			$pattern = '/' . implode('|', $exclude) . '/';
		}
		foreach ($this->_paths as $path) {
			$Folder = new Folder($path);
			$files = $Folder->findRecursive('.*\.(' . $this->fileTypes . ')', true);
			if (!empty($pattern)) {
				foreach ($files as $i => $file) {
					if (preg_match($pattern, $file)) {
						unset($files[$i]);
					}
				}
				$files = array_values($files);
			}
			$this->_files = array_merge($this->_files, $files);
		}
	}

	/**
	 * Extract tokens out of all files to be processed
	 *
	 * @return void
	 */
	protected function _extractTokens() {
		foreach ($this->_files as $file) {
			$this->_file = $file;
			$this->out(__d('cake_console', 'Processing %s...', $file));

			$allTokens = $this->_getTokens($file);

			$this->_tokens = array();
			foreach ($allTokens as $token) {
				if (!is_array($token) || ($token[0] != T_WHITESPACE && $token[0] != T_INLINE_HTML)) {
					$this->_tokens[] = $token;
				}
			}
			unset($allTokens);
			$this->_parse('__', array('singular'));
			$this->_parse('__n', array('singular', 'plural'));
			$this->_parse('__d', array('domain', 'singular'));
			$this->_parse('__c', array('singular', 'category'));
			$this->_parse('__dc', array('domain', 'singular', 'category'));
			$this->_parse('__dn', array('domain', 'singular', 'plural'));
			$this->_parse('__dcn', array('domain', 'singular', 'plural', 'count', 'category'));
		}
	}

	/**
	 * Parse file for tokens
	 * 
	 * @param string $file
	 * @return array
	 */
	protected function _getTokens($file) {
		$code = file_get_contents($file);
		if (strtoupper(pathinfo($file, PATHINFO_EXTENSION)) == 'JS') {
			$Tokenizer = new PHP_CodeSniffer_Tokenizers_JS;
			$tokens = $Tokenizer->tokenizeString($code);
			array_walk($tokens, function(&$token) {
				if (in_array($token['code'], array(
							T_OPEN_PARENTHESIS, T_CLOSE_PARENTHESIS, T_SEMICOLON, T_STRING_CONCAT, T_WHITESPACE
						))) {
					$token = $token['content'];
				} else {
					$token = array(
						$token['code'],
						$token['content'],
						'JS'
					);
				}
			});
			return $tokens;
		} else {
			return token_get_all($code);
		}
	}

	/**
	 * hhvm fix for bug https://github.com/facebook/hhvm/issues/4007
	 */
	protected function _hhvmFix() {
		if (isset($this->params['extract-core'])) {
			$this->_extractCore = (strtolower($this->params['extract-core']) != 'no');
		}
		if (!$this->_extractCore) {
			$this->_paths = array_filter($this->_paths, function($path) {
				return $path != CAKE;
			});
		}
	}

	/**
	 * Save messages into DB
	 */
	protected function _writeDb() {
		foreach ($this->_translations as $category => $domains) {
			foreach ($domains as $domain => $translations) {
				foreach ($translations as $msgid => $details) {
					$this->_writeOneDb($msgid, $details);
				}
			}
		}
	}

	/**
	 * Save one record
	 * 
	 * @param string $msgid
	 * @param array $details
	 */
	protected function _writeOneDb($msgid, array $details) {
		$references = array();
		$isJs = false;
		foreach ($details['references'] as $file => $lines) {
			foreach ($lines as $line) {
				$references[] = array(
					'file' => preg_replace('/^' . preg_quote(ROOT, '/') . '/', '', $file),
					'line' => is_int($line) ? $line : 0,
					'comment' => is_string($line) ? $line : ''
				);
				$isJs = $isJs || ($line === 'JS');
			}
		}
		$id = $this->Message->field('id', array(
			'name' => $msgid
		));
		$this->Message->saveAssociatedChanged(array(
			$this->Message->alias => array(
				'id' => $id,
				'name' => $msgid,
				'js' => $isJs
			),
			$this->Message->References->alias => $references
		));
	}

	/**
	 * {@inheritdoc}
	 *
	 * @param string $path Path to folder
	 * @return bool true if it exists and is writable, false otherwise
	 */
	protected function _isPathUsable($path) {
		return (rtrim($path, DS) === 'db') || is_dir($path) && is_writable($path);
	}

}

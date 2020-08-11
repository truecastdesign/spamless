<?php
namespace Truecast;

/**
 * 
 *
 * @package True Framework 6
 * @author Daniel Baldwin
 * @version 1.0.0
 * @copyright 2020 Truecast Design Studio
 */
class Spamless
{
	private $data = [];
	private $tests = [];
	private $errors = [];
	private $errorMsgs = [
		'basic'=>"The content you submitted appears to be SPAM.",
		'url'=>"URLs not allowed!",
		'html'=>"HTML not allowed!"
	];
	
	/**
	 * Pass in associative array or key/value object with values to check
	 *
	 * @param array|object $data ['key'=>'value']
	 */
	public function __construct($data)
	{
		$this->data = (array) $data;
	}

	/**
	 * Pass in tests to check values with
	 *
	 * @param array $tests ['basic','url','html']
	 * @return self
	 */
	public function tests(array $tests)
	{
		$this->tests = $tests;
		return $this;
	}

	/**
	 * Checks the data array values which keys are passed 
	 *
	 * @param array $fields array keys in the data array
	 * @return bool
	 */
	public function check(array $fields)
	{
		$results = true;
		foreach ($fields as $field) {
			foreach ($this->tests as $test) {
				if ($testResult = $this->$test($this->data[$field])) {
					$this->errors[] = $this->errorMsgs[$test];
					$results = false;
				}	
			}
		}
		return $results;
	}

	public function basic(string $value=''): bool
	{
		$result[] = $this->tooManyConsonants($value);
		$result[] = $this->keywordSearch($value);
		$result[] = \Truecast\Gibberish::test($value);
		return (bool) array_sum($result);
	}

	/**
	 * detects if there is a url in the message text
	 *
	 * @param string $value - message text
	 * @return bool true if urls are detected
	 * @author Daniel Baldwin - danb@truecastdesign.com
	 **/
	public function url(string $value=''): bool
	{
		preg_match('/www\.|http:|https:\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_‌​a-z]{2,5}'.'((:[0-9]‌​{1,5})?\/.*)?$/i', $value, $matches);
		preg_match("/[-a-zA-Z0-9@:%_\+.~#?&\/=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&\/=]*)?/i", $value, $matches2);
		return (count($matches) > 0 OR count($matches2) > 0)? true:false;
	}

	/**
	 * check if the message has words will more than 6 consonants in a row. Usually means it is invalid words and spam
	 *
	 * @param string message text
	 * @return bool spam if true
	 * @author Daniel Baldwin - danb@truecastdesign.com
	 **/
	public function tooManyConsonants(string $value=''): bool
	{
		preg_match('/[bcdfghjklmnpqrstvwxz]{6}/i', $value, $matches);
		return count($matches) > 0? true:false;
	}

	/**
	 * search message for spam keywords
	 *
	 * @param string - $value - message to search
	 * @return bool - true if we think it is spam
	 * @author Daniel Baldwin - danb@truecastdesign.com
	 **/
	public function keywordSearch(string $value=''): bool
	{
		$keywords = require 'keywords.php';	
		$hit = false;

		foreach ($keywords as $key) {
			if (stripos($value, $key) !== false)
				$hit = true;
		}
		return $hit;
	}

	/**
	 * Check for html in value
	 *
	 * @param string $value
	 * @return bool
	 */
	public function html(string $value=''): bool
	{
		return (strip_tags($value) != $value)? true:false;
	}

	/**
	 * Return errors array
	 *
	 * @return bool
	 */
	public function errors(): array
	{
		return array_unique($this->errors);
	}
}
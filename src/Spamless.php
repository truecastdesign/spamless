<?php
namespace Truecast;

/**
 * 
 *
 * @package True Framework 6
 * @author Daniel Baldwin
 * @version 1.1.2
 * @copyright 2026 Truecast Design Studio
 */
class Spamless
{
	private $data = [];
	private $tests = [];
	private $errors = [];
	private $errorMsgs = [
		'url'=>"URLs not allowed!",
		'html'=>"HTML not allowed!",
		'russian'=>"Russian characters not allowed!",
		'keywords'=>"The content you submitted appears to be SPAM.",
		'gibberish'=>"The content you submitted does not appear to be actual English words.",
		'underscores'=>"Do not combine your words with underscores!",
		'uppercase'=>"The content you submitted appears to be SPAM. UC",
		'salesEmail'=>"This appears to be a sales or spam email."
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

	public function gibberish($value=''): bool
	{
		if (empty($value))
			return false;
		preg_match('/[bcdfghjklmnpqrstvwxz]{6}/i', $value, $matches);
		return count($matches) > 0? true:false;
		#return \Truecast\Gibberish::test($value);
	}

	public function underscores($value=''): bool
	{
		if (empty($value))
			return false;
		return (strstr($value, '_') !== false);
	}

	/**
	 * Check if all the alpha characters in a line are uppercase.
	 *
	 * @param string $str
	 * @return bool
	 */
	public function uppercase($str='')
	{
		if (empty($str))
			return false;
		$lines = explode("\n",$str);
		foreach ($lines as $line) {
			$result[] = (ctype_upper(preg_replace("/[^A-Za-z]/", '', $line)));
		}
		return (bool) array_sum($result);
	}

	/**
	 * detects if there is a url in the message text
	 *
	 * @param string $value - message text
	 * @return bool true if urls are detected
	 * @author Daniel Baldwin - danb@truecastdesign.com
	 **/
	public function url($value=''): bool
	{
		if (empty($value))
			return false;
		preg_match('/www\.|http:|https:\/\/[a-z0-9_]+([\-\.]{1}[a-z_0-9]+)*\.[_‌​a-z]{2,5}'.'((:[0-9]‌​{1,5})?\/.*)?$/i', $value, $matches);
		preg_match("/[-a-zA-Z0-9@:%_\+.~#?&\/=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&\/=]*)?/i", $value, $matches2);
		return (count($matches) > 0 OR count($matches2) > 0)? true:false;
	}

	/** NOT USED
	 * check if the message has words will more than 6 consonants in a row. Usually means it is invalid words and spam
	 *
	 * @param string message text
	 * @return bool spam if true
	 * @author Daniel Baldwin - danb@truecastdesign.com
	 **/
	public function tooManyConsonants($value=''): bool
	{
		if (empty($value))
			return false;
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
	public function keywords($value=''): bool
	{
		if (empty($value))
			return false;

		$keywords = require 'keywords.php';
		$hit = false;

		foreach ($keywords as $key) {
			if (stripos($value, $key) !== false)
				$hit = true;
		}
		return $hit;
	}

	/**
	 * Check for Russian characters
	 *
	 * @param string $text
	 * @return boolean - true if is contains russian characters
	 */
	public function russian($text='') {
		if (empty($text))
			return false;
		return preg_match('/[А-Яа-яЁё]/u', $text);
	}

	/**
	 * Check for html in value
	 *
	 * @param string $value
	 * @return bool
	 */
	public function html($value=''): bool
	{
		if (empty($value))
			return false;
		return (strip_tags($value) != $value)? true:false;
	}

	/**
	 * Detect SEO/sales spam emails with common cold outreach patterns
	 *
	 * @param string $value - message text to check
	 * @return bool - true if appears to be a sales/spam email
	 */
	public function salesEmail($value=''): bool
	{
		if (empty($value))
			return false;

		// Convert to lowercase for case-insensitive matching
		$lowerValue = strtolower($value);

		// Common SEO spam phrases
		$seoSpamPhrases = [
			'google\'s 1st page',
			'google search index',
			'search engine optimization',
			'improve your website\'s position',
			'appear in google search',
			'organic search ranking',
			'seo setup',
			'searchregister.',
			'register your website',
			'submit your website',
			'insert your website',
			'improve your ranking',
			'increase your traffic',
			'generate more sales',
			'online traffic',
			'expert team of professionals',
			'backend of your website',
			'google when people search',
			'squarespace, shopify, wix, wordpress'
		];

		// Video/service spam phrases
		$servicePhrases = [
			'impactful video to advertise',
			'our videos cost just',
			'voice-over and video',
			'30 second video',
			'60 seconds',
			'previous work',
			'send you a proposal'
		];

		// Generic cold outreach patterns
		$coldOutreachPhrases = [
			'i just visited',
			'i came across your website',
			'kindly provide me your',
			'phone number and email',
			'your phone number',
			'if interested, kindly',
			'if you are interested',
			'let me know if you\'re interested'
		];

		// Combine all spam phrases
		$allPhrases = array_merge($seoSpamPhrases, $servicePhrases, $coldOutreachPhrases);

		// Count how many spam phrases are detected
		$matchCount = 0;
		foreach ($allPhrases as $phrase) {
			if (stripos($lowerValue, $phrase) !== false) {
				$matchCount++;
			}
		}

		// If 2 or more spam phrases detected, flag as sales email
		return $matchCount >= 2;
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
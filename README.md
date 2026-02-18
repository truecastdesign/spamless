Spamless - Spam detection class
=======================================

Install
-------

To install with composer:

```sh
composer require truecastdesign/spamless
```

Requires PHP 7.1 or newer.

Usage
-----

Here's a basic usage example:


```php
# composer autoloader
require '/path/to/vendor/autoload.php';

$Spam = new Truecast\Spamless(['name'=>'My Name', 'message'=>'This is a message', 'phone'=>'This value is not checked']);

# 'check' should be passed the value keys you want to check
# 'tests' should be passed the tests to perform

if ($Spam->tests(['gibberish','uppercase','underscores','keywords','russian','url','html','salesEmail'])->check(['name','phone', 'message'])) {
	echo 'valid';
} else {
	echo 'not valid: ';
	print_r($Spam->errors());
}
```

Available Tests
---------------

| Test | Description |
|------|-------------|
| `gibberish` | Detects strings with 6 or more consecutive consonants, which typically indicates non-English or random text. |
| `uppercase` | Flags content where all alphabetic characters on a line are uppercase, a common spam indicator. |
| `underscores` | Detects words combined with underscores (e.g. `buy_now_cheap`). |
| `keywords` | Searches for known spam keywords from a built-in keyword list. |
| `russian` | Detects Cyrillic (Russian) characters. |
| `url` | Detects URLs or domain-like patterns in the text. |
| `html` | Detects HTML tags in the text. |
| `salesEmail` | Detects cold outreach and SEO/sales spam patterns. Flags a message if 2 or more known spam phrases are found, including common SEO pitches, video service offers, and generic cold outreach openers. |

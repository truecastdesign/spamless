Spamless - Spam detection class
=======================================

Version: v1.0.0

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
# 'with' should be passed the tests to proform
# 'basic' check values for too many consonants in a row, spammy keywords, and gibberish.
# 'url' checks values for urls
# 'html' check values for html

if ($Spam->check(['name','message'])->with(['basic','url','html'])) {
	echo 'valid';
else
	echo 'not valid: ';
print_r($Spam->errors());
```


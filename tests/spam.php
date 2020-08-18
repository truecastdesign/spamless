<?php
use PHPUnit\Framework\TestCase;

require '/Users/pedee/Sites/git-repos/spamless/src/Spamless.php';

class spam extends TestCase
{
	/** @test */
	public function testGribbish()
	{
		$Spamless = new \Spamless([]);
		$cleanContent = ['What is the meaning of life.', 'This website is really neat', 'I would like to know how you do things.', 'Thansk or all that you do.', 'can anybody explain exactly what these spammers are trying to do', 'A solution that often helps when fighting against spam', 'a bunch of great options from everybody', "I'm interested in knowing how to", "Jim", "Dan", "Sam", "johnnietheblack", "Pascal MARTIN", "ssokolow", "As far as trying to block them is concerned", "they certainly don't read the form or support CSS", "Then any form submit with a value in the unfillable field is definitely spam and can be ignored", "I see this on forms where no feedback email is generated"];

		$spamContent = ["jwpphdwy","qqqggggwncceb"];

		foreach ($cleanContent as $text)
			$this->assertTrue(!$Spamless->gribbish($text));
			
		foreach ($spamContent as $text)
			$this->assertTrue($Spamless->gribbish($text));
	}
}
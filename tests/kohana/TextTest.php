<?php

/**
 * Tests the kohana text class (Kohana_Text)
 *
 * @group kohana
 */
Class Kohana_TextTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Sets up the test enviroment
	 */
	function setUp()
	{
		Text::alternate();
	}

	/**
	 * Data provider for testLimitWords
	 *
	 * @return array Array of test data
	 */
	function providerLimitWords()
	{
		return array
		(
			array('', '', 100, NULL),
			array('&#8230;', 'The rain in spain', -10, NULL),
			array('The rain&#8230;', 'The rain in spain', 2, NULL),
			array('The rain...', 'The rain in spain', 2, '...'),
		);
	}
	
	/**
	 *
	 * @test
	 * @dataProvider providerLimitWords
	 * @covers Text::limit_words
	 */
	function testLimitWords($expected, $str, $limit, $end_char)
	{
		$this->assertSame($expected, Text::limit_words($str, $limit, $end_char));
	}

	/**
	 * Provides test data for testLimitChars()
	 *
	 * @return array Test data
	 */
	function providerLimitChars()
	{
		return array
			(
				// Some basic tests
				array('', '', 100, NULL, FALSE),
				array('&#8230;', 'BOO!', -42, NULL, FALSE),

				// 
				array('making php bet&#8230;', 'making php better for the sane', 14, NULL, FALSE),
				array('making php better&#8230;', 'making php better for the sane', 14, NULL, TRUE),
			);
	}

	/**
	 * Tests Text::limit_chars()
	 *
	 * @test
	 * @dataProvider providerLimitChars
	 */
	function testLimitChars($expected, $str, $limit, $end_char, $preserve_words)
	{
		$this->assertSame($expected, Text::limit_chars($str, $limit, $end_char, $preserve_words));
	}

	/**
	 * Test Text::alternate()
	 *
	 * @test
	 * @covers Text::alternate
	 * @group testdox
	 */
	function testAlternateAlternatesBetweenParameters()
	{
		$values = array('good', 'bad', 'ugly');

		$this->assertSame('good', call_user_func_array(array('Text', 'alternate'), $values));
		$this->assertSame('bad',  call_user_func_array(array('Text', 'alternate'), $values));
		$this->assertSame('ugly', call_user_func_array(array('Text', 'alternate'), $values));
		
		$this->assertSame('good', call_user_func_array(array('Text', 'alternate'), $values));
	}

	/**
	 * Tests Text::alternate()
	 *
	 * @test
	 * @covers Text::alternate
	 * @group testdox
	 */
	function testAlternateResetsWhenCalledWithNoParamsAndReturnsEmptyString()
	{
		$values = array('yes', 'no', 'maybe');
		
		$this->assertSame('yes', call_user_func_array(array('Text', 'alternate'), $values));
		
		$this->assertSame('', Text::alternate());	

		$this->assertSame('yes', call_user_func_array(array('Text', 'alternate'), $values));
	}

	/**
	 * Provides test data for testReducdeSlashes()
	 *
	 * @returns array Array of test data 
	 */
	function providerReduceSlashes()
	{
		return array
			(
				array('/', '//'),
				array('/google/php/kohana/', '//google/php//kohana//'),
			);
	}

	/**
	 * Covers Text::reduce_slashes()
	 *
	 * @test
	 * @dataProvider providerReduceSlashes
	 * @covers Text::reduce_slashes
	 */
	function testReduceSlashes($expected, $str)
	{
		$this->assertSame($expected, Text::reduce_slashes($str));
	}

	/**
	 * Provides test data for testCensor()
	 *
	 * @return array Test data
	 */
	function providerCensor()
	{

		return array
			(
				// If the replacement is 1 character long it should be repeated for the length of the removed word
				array("A donkey is also an ***", 'A donkey is also an ass', array('ass'), '*', TRUE),
				array("Cake### isn't nearly as good as kohana###", "CakePHP isn't nearly as good as kohanaphp", array('php'), '#', TRUE),
				// If it's > 1 then it's just replaced straight out
				array("If you're born out of wedlock you're a --expletive--", "If you're born out of wedlock you're a child", array('child'), '--expletive--', TRUE),
			);
	}

	/**
	 * Tests Text::censor
	 *
	 * @test
	 * @dataProvider providerCensor
	 * @covers Text::censor
	 */
	function testCensor($expected, $str, $badwords, $replacement, $replace_partial_words)
	{
		$this->assertSame($expected, Text::censor($str, $badwords, $replacement, $replace_partial_words));
	}

	/**
	 *
	 */
	function providerSimilar()
	{
		return array
			(
				// TODO: add some more cases
				array('foo', array('foobar', 'food', 'fooberry')),
			);
	}

	/**
	 * Tests Text::similar()
	 *
	 * @test
	 * @dataProvider providerSimilar
	 * @covers Text::similar
	 */
	function testSimilar($expected, $words)
	{
		$this->assertSame($expected, Text::similar($words));
	}

	function providerBytes()
	{
		return array
			(
				// TODO: cover the other units
				array('256.00 B', 256, NULL, NULL, TRUE),
				array('1.02 kB', 1024, NULL, NULL, TRUE),

				// In case you need to know the size of a floppy disk in petabytes
				array('0.00147 GB', 1.44 * 1000 * 1024, 'GB', '%01.5f %s', TRUE),

				// SI is the standard, but lets deviate slightly
				array('1.00 MiB', 1024 * 1024, 'MiB', NULL, FALSE),
			);
	}

	/**
	 * Tests Text::bytes()
	 *
	 * @test
	 * @dataProvider providerBytes
	 * @covers Text::bytes
	 */
	function testBytes($expected, $bytes, $force_unit, $format, $si)
	{
		$this->assertSame($expected, Text::bytes($bytes, $force_unit, $format, $si));
	}

	/**
	 * Provides test data for testWidont()
	 *
	 * @return array Test data
	 */
	function providerWidont()
	{
		return array
			(
				array('No gain, no&nbsp;pain', 'No gain, no pain'),
				array("spaces?what'rethey?", "spaces?what'rethey?"),
				array('', ''),
			);
	}

	/**
	 * Tests Text::widont()
	 *
	 * @test
	 * @dataProvider providerWidont
	 * @covers Text::widont
	 */
	function testWidont($expected, $string)
	{
		$this->assertSame($expected, Text::widont($string));
	}


	/**
	 * This checks that auto_link_emails() respects word boundaries and does not
	 * just blindly replace all occurences of the email address in the text.
	 *
	 * In the sample below the algorithm was replacing all occurences of voorzitter@xxxx.com
	 * inc the copy in the second list item.
	 *
	 * It was updated in 6c199366efc1115545ba13108b876acc66c54b2d to respect word boundaries
	 *
	 * @test
	 * @covers Text::auto_link_emails
	 * @ticket 2772
	 */
	function testAutoLinkEmailsRespectsWordBoundaries()
	{
		$original = '<ul>
						<li>voorzitter@xxxx.com</li>
						<li>vicevoorzitter@xxxx.com</li>
					</ul>';
	
		$this->assertFalse(strpos('vice', Text::auto_link_emails($original)));
	}

}

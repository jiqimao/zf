<?php

use \zf\ClosureSet;

class ClosureSetTest extends PHPUnit_Framework_TestCase
{
	public function testRegistered()
	{
		$this->helper->register('h1',function(){return false;});
		$this->assertTrue($this->helper->registered('h1'));
		$this->assertFalse($this->helper->h1());
	}

	public function testRegister()
	{ 
		$this->helper->register(['notset','alias'=>'inc','dec']);

		$this->assertTrue($this->helper->registered('notset'));
		$this->assertTrue($this->helper->registered('alias'));
		$this->assertTrue($this->helper->registered('dec'));

		$this->assertSame($this->helper->alias(0), 1);
		$this->assertSame($this->helper->dec(1), 0);

		$this->helper->register(['h4'=> function(){return true;}]);
		$this->assertTrue($this->helper->h4());
	}

	public function testDelayed()
	{
		$this->helper->register('swap', function($arg, $arg2){
			return [$arg2, $arg];
		});
		$delayed = $this->helper->delayed->swap(1, 2);
		$this->assertSame($delayed(), [2, 1]);
	}

	public function testLoadClosure()
	{
		$this->assertSame($this->helper->inc(1), 2);

		$this->assertSame($this->helper->math->sum(1,2,3), 6);

		$this->assertSame(6, $this->helper->{'math.sum'}(1,2,3));
		$this->assertSame(6, $this->helper->{'math/sum'}(1,2,3));
	}

	public function testKeywordArguments()
	{
		$this->helper->register('name', function($firstname, $lastname='bar'){
			return $firstname . ' ' . $lastname;
		});

		$this->assertEquals('foo bar', $this->helper->__apply('name', ['firstname'=> 'foo']));
		$this->assertEquals('foo oof', $this->helper->__apply('name', ['lastname'=> 'oof', 'firstname'=> 'foo']));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testKeywordArgumentsException()
	{
		$this->helper->register('name', function($firstname, $lastname='bar'){
			return $firstname . ' ' . $lastname;
		});

		$this->assertEquals('foo bar', $this->helper->__apply('name', ['lastname'=> 'foo']));
	}

	public function setup()
	{
		$this->helper = new ClosureSet(null, __DIR__ . DIRECTORY_SEPARATOR . 'closures');
	}
}

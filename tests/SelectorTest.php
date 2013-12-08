<?php

use PHPHtmlParser\Selector;
use PHPHtmlParser\Dom\HtmlNode;
use PHPHtmlParser\Dom\Tag;
use Mockery as m;

class SelectorTest extends PHPUnit_Framework_TestCase {
	
	public function tearDown()
	{
		m::close();
	}

	public function testParseSelectorStringId()
	{
		$selector  = new Selector('#all');
		$selectors = $selector->getSelectors();
		$this->assertEquals('id', $selectors[0][0]['key']);
	}

	public function testParseSelectorStringClass()
	{
		$selector  = new Selector('div.post');
		$selectors = $selector->getSelectors();
		$this->assertEquals('class', $selectors[0][0]['key']);
	}

	public function testParseSelectorStringAttribute()
	{
		$selector  = new Selector('div[visible=yes]');
		$selectors = $selector->getSelectors();
		$this->assertEquals('yes', $selectors[0][0]['value']);
	}

	public function testParseSelectorStringNoKey()
	{
		$selector  = new Selector('div[!visible]');
		$selectors = $selector->getSelectors();
		$this->assertTrue($selectors[0][0]['noKey']);
	}

	public function testFind()
	{
		$root   = new HtmlNode(new Tag('root'));
		$parent = new HtmlNode(new Tag('div'));
		$child1 = new HtmlNode(new Tag('a'));
		$child2 = new HtmlNode(new Tag('p'));
		$parent->addChild($child1);
		$parent->addChild($child2);
		$root->addChild($parent);

		$selector = new Selector('div a');
		$this->assertEquals($child1->id(), $selector->find($root)[0]->id());
	}

	public function testFindId()
	{
		$parent = new HtmlNode(new Tag('div'));
		$child1 = new HtmlNode(new Tag('a'));
		$child2 = new HtmlNode(new Tag('p'));
		$child2->getTag()->setAttributes([
			'id' => 'content',
		]);
		$parent->addChild($child1);
		$parent->addChild($child2);

		$selector = new Selector('#content');
		$this->assertEquals($child2->id(), $selector->find($parent)[0]->id());
	}

	public function testFindClass()
	{
		$parent = new HtmlNode(new Tag('div'));
		$child1 = new HtmlNode(new Tag('a'));
		$child2 = new HtmlNode(new Tag('p'));
		$child3 = new HtmlNode(new Tag('a'));
		$child3->getTag()->setAttributes([
			'class' => 'link',
		]);
		$parent->addChild($child1);
		$parent->addChild($child2);
		$parent->addChild($child3);

		$selector = new Selector('.link');
		$this->assertEquals($child3->id(), $selector->find($parent)[0]->id());
	}

	public function testFindClassMultiple()
	{
		$parent = new HtmlNode(new Tag('div'));
		$child1 = new HtmlNode(new Tag('a'));
		$child2 = new HtmlNode(new Tag('p'));
		$child3 = new HtmlNode(new Tag('a'));
		$child3->getTag()->setAttributes([
			'class' => 'link outer',
		]);
		$parent->addChild($child1);
		$parent->addChild($child2);
		$parent->addChild($child3);

		$selector = new Selector('.outer');
		$this->assertEquals($child3->id(), $selector->find($parent)[0]->id());
	}

	public function testFindWild()
	{
		$root   = new HtmlNode(new Tag('root'));
		$parent = new HtmlNode(new Tag('div'));
		$child1 = new HtmlNode(new Tag('a'));
		$child2 = new HtmlNode(new Tag('p'));
		$child3 = new HtmlNode(new Tag('a'));
		$root->addChild($parent);
		$parent->addChild($child1);
		$parent->addChild($child2);
		$child2->addChild($child3);

		$selector = new Selector('div * a');
		$this->assertEquals($child3->id(), $selector->find($root)[0]->id());
	}

	public function testFindMultipleSelectors()
	{
		$root   = new HtmlNode(new Tag('root'));
		$parent = new HtmlNode(new Tag('div'));
		$child1 = new HtmlNode(new Tag('a'));
		$child2 = new HtmlNode(new Tag('p'));
		$child3 = new HtmlNode(new Tag('a'));
		$root->addChild($parent);
		$parent->addChild($child1);
		$parent->addChild($child2);
		$child2->addChild($child3);

		$selector = new Selector('a, p');
		$this->assertEquals(3, count($selector->find($root)));
	}
}

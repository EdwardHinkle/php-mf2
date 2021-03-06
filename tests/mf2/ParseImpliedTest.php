<?php
/**
 * Tests of the parsing methods within mf2\Parser
 */

namespace mf2\Parser\test;

// Include Parser.php
$autoloader = require_once dirname(__DIR__) . '/../mf2/Parser.php';

use mf2\Parser,
	PHPUnit_Framework_TestCase,
	DateTime;

class ParseImpliedTest extends PHPUnit_Framework_TestCase
{	
	public function setUp()
	{
		date_default_timezone_set('Europe/London');
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedPNameFromNodeValue()
	{
		$input = '<span class="h-card">The Name</span>';
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertArrayHasKey('name', $output['items'][0]['properties']);
		$this -> assertEquals('The Name', $output['items'][0]['properties']['name'][0]);
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedPNameFromImgAlt()
	{
		$input = '<img class="h-card" src="" alt="The Name" />';
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertArrayHasKey('name', $output['items'][0]['properties']);
		$this -> assertEquals('The Name', $output['items'][0]['properties']['name'][0]);
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedPNameFromNestedImgAlt()
	{
		$input = '<div class="h-card"><img src="" alt="The Name" /></div>';
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertArrayHasKey('name', $output['items'][0]['properties']);
		$this -> assertEquals('The Name', $output['items'][0]['properties']['name'][0]);
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedPNameFromDoublyNestedImgAlt()
	{
		$input = '<div class="h-card"><span><img src="" alt="The Name" /></span></div>';
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertArrayHasKey('name', $output['items'][0]['properties']);
		$this -> assertEquals('The Name', $output['items'][0]['properties']['name'][0]);
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedUPhotoFromImgSrc()
	{
		$input = '<img class="h-card" src="http://example.com/img.png" alt="" />';
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this -> assertEquals('http://example.com/img.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedUPhotoFromNestedImgSrc()
	{
		$input = '<div class="h-card"><img src="http://example.com/img.png" alt="" /></div>';
		$parser = new Parser($input);
		$output = $parser -> parse();
				
		$this -> assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this -> assertEquals('http://example.com/img.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedUPhotoFromDoublyNestedImgSrc()
	{
		$input = '<div class="h-card"><span><img src="http://example.com/img.png" alt="" /></span></div>';
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertArrayHasKey('photo', $output['items'][0]['properties']);
		$this -> assertEquals('http://example.com/img.png', $output['items'][0]['properties']['photo'][0]);
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedUUrlFromAHref()
	{
		$input = '<a class="h-card" href="http://example.com/">Some Name</a>';
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertArrayHasKey('url', $output['items'][0]['properties']);
		$this -> assertEquals('http://example.com/', $output['items'][0]['properties']['url'][0]);
	}
	
	/**
	 * @group parseH
	 * @group implied
	 */
	public function testParsesImpliedUUrlFromNestedAHref()
	{
		$input = '<span class="h-card"><a href="http://example.com/">Some Name</a></span>';
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertArrayHasKey('url', $output['items'][0]['properties']);
		$this -> assertEquals('http://example.com/', $output['items'][0]['properties']['url'][0]);
	}
	
	public function testMultipleImpliedHCards()
	{
		$input = '<span class="h-card">Frances Berriman</span>
 
<a class="h-card" href="http://benward.me">Ben Ward</a>
 
<img class="h-card" alt="Sally Ride" 
     src="http://upload.wikimedia.org/wikipedia/commons/a/a4/Ride-s.jpg"/>
 
<a class="h-card" href="http://tantek.com">
 <img alt="Tantek Çelik" src="http://ttk.me/logo.jpg"/>
</a>';
		$expected = '{
	"items": [{
		"type": ["h-card"],
		"properties": {
			"name": ["Frances Berriman"]
		}
	},
	{
		"type": ["h-card"],
		"properties": {
			"name": ["Ben Ward"],
			"url": ["http://benward.me"]
		}
	},
	{
		"type": ["h-card"],
		"properties": {
			"name": ["Sally Ride"],
			"photo": ["http://upload.wikimedia.org/wikipedia/commons/a/a4/Ride-s.jpg"]
		}
	},
	{
		"type": ["h-card"],
		"properties": {
			"name": ["Tantek Çelik"],
			"url": ["http://tantek.com"],
			"photo": ["http://ttk.me/logo.jpg"]
		}
	}]
}';
		
		$parser = new Parser($input);
		$output = $parser -> parse();
		
		$this -> assertJsonStringEqualsJsonString(json_encode($output), $expected);
	}
}

// EOF tests/mf2/testParser.php
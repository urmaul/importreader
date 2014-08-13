<?php

use importreader\Reader;

class OdsTest extends PHPUnit_Framework_TestCase
{
    public function testNormal()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.ods';
        $reader->colsCount = 3;
        $reader->init();
        
        $expecteds = array(
            array('first', 'second', 'third'),
            array('1', '2', '3'),
            array('s1', 's2', 's3'),
        );
        
        foreach ($reader as $actual) {
            $expected = array_shift($expecteds);
            $this->assertEquals($expected, $actual);
        }
    }
    
    public function testNormal2Cols()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.ods';
        $reader->colsCount = 2;
        $reader->init();
        
        $expecteds = array(
            array('first', 'second'),
            array('1', '2'),
            array('s1', 's2'),
        );
        
        foreach ($reader as $actual) {
            $expected = array_shift($expecteds);
            $this->assertEquals($expected, $actual);
        }
    }
    
    public function testLabels()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.ods';
        $reader->useLabels = true;
        $reader->labels = array('first', 'second', 'third');
        $reader->ignoredRowsCount = 1;
        $reader->init();
        
        $expecteds = array(
            array('first' => '1',  'second' => '2',  'third' => '3'),
            array('first' => 's1', 'second' => 's2', 'third' => 's3'),
        );
        
        foreach ($reader as $actual) {
            $expected = array_shift($expecteds);
            $this->assertEquals($expected, $actual);
        }
    }
    
	public function testReadAll()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.ods';
        $reader->colsCount = 3;
        $reader->init();
        
        $expecteds = array(
            array('first', 'second', 'third'),
            array('1', '2', '3'),
            array('s1', 's2', 's3'),
        );
        
        $this->assertEquals($expecteds, $reader->readAll());
    }
}

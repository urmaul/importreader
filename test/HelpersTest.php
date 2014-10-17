<?php

use importreader\Reader;

class HelpersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException importreader\Exception
     */
    public function testColsCountException()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.xls';
        $reader->init();
    }
    
    public function testNormal()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.xls';
        $reader->colsCount = 3;
        $reader->init();
        
        $expected = array(
            array('first', 'second', 'third'),
            array('1', '2', '3'),
            array('s1', 's2', 's3'),
        );
        
        $this->assertEquals($expected, $reader->readAll());
    }
    
    public function testNormal2Cols()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.xls';
        $reader->colsCount = 2;
        $reader->init();
        
        $expected = array(
            array('first', 'second'),
            array('1', '2'),
            array('s1', 's2'),
        );
        
        $this->assertEquals($expected, $reader->readAll());
    }
    
    public function testLabels()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.xls';
        $reader->useLabels = true;
        $reader->labels = array('first', 'second', 'third');
        $reader->ignoredRowsCount = 1;
        $reader->init();
        
        $expected = array(
            array('first' => '1',  'second' => '2',  'third' => '3'),
            array('first' => 's1', 'second' => 's2', 'third' => 's3'),
        );
        
        $this->assertEquals($expected, $reader->readAll());
    }
    
    
    public function readColProvider()
    {
        return array(
            array(false, 0, array('first', 1, 's1')),
            array(false, 1, array('second', 2, 's2')),
            array(false, 2, array('third', 3, 's3')),
            array(true, 0, array(1, 's1')),
            array(true, 1, array(2, 's2')),
            array(true, 2, array(3, 's3')),
        );
    }
    
    /**
     * @dataProvider readColProvider
     */
    public function testReadCol($useLabels, $index, $expected)
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.xls';
        $reader->useLabels = $useLabels;
        $reader->colsCount = 3;
        $reader->init();
        
        $this->assertEquals($expected, $reader->readColumn($index));
    }
    
    /**
     * @expectedException importreader\Exception
     */
    public function testReadColException()
    {
        $reader = new Reader();
        $reader->filePath = __DIR__ . '/files/3cols.xls';
        $reader->useLabels = false;
        $reader->colsCount = 3;
        $reader->init();
        
        $reader->readColumn(10);
    }
}

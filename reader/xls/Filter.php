<?php

namespace importreader\reader\xls;

class Filter implements \PHPExcel_Reader_IReadFilter
{
    protected $colsRange;
    protected $startRow = 0;
    protected $chunkSize = 0;
    protected $endRow = 0;
    
    public function __construct($colsCount)
    {
        $toColumn = \PHPExcel_Cell::stringFromColumnIndex($colsCount - 1);
        $this->colsRange = range('A', $toColumn);
    }
    
    public function setChunk($startRow, $chunkSize)
    { 
        $this->chunkSize = $chunkSize;
        $this->startRow  = $startRow;
        $this->endRow    = $startRow + $chunkSize;
    } 
    
    public function readCell($column, $row, $worksheetName = '')
    {
        return
            ($this->chunkSize == 0 || ($row >= $this->startRow && $row < $this->endRow)) &&
            in_array($column,$this->colsRange);
    }
}

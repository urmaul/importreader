<?php

namespace importreader\reader;

class Xls extends AbstractReader
{
    /**
     * PHPExcel directory path alias
     * @var string 
     */
    public $phpExcelPathAlias = 'ext.phpexcel';

    /**
     * @var \PHPExcel_Worksheet 
     */
    protected $reader;

    protected $excel;
    
    /**
     * @var xls\Filter 
     */
    protected $filter;

    /**
     * @var \PHPExcel_Worksheet 
     */
    protected $sheet;
    
    
    protected $lastRowPosition = null;
    protected $lastRow;
    
    //protected $cahcedRows;


    public function init()
    {
        parent::init();
        
        spl_autoload_unregister(array('YiiBase','autoload'));
		$path = \Yii::getPathOfAlias($this->phpExcelPathAlias . '.PHPExcel');
		require_once $path . '.php';
        
        $this->filter = \Yii::createComponent('\\'. __NAMESPACE__ .'\\xls\\Filter', $this->colsCount);
        
        $reader = \PHPExcel_IOFactory::createReaderForFile($this->filePath);
        /* @var $reader \PHPExcel_Reader_IReader */
        
        $reader->setReadDataOnly(true);
        $reader->setReadFilter($this->filter);
        
        $sheets = $reader->listWorksheetNames($this->filePath);
        $reader->setLoadSheetsOnly( array($sheets[0]) );
        
        $this->reader = $reader;
        
        //$this->filter->setChunk(1, 1);
        
        $this->excel = $this->reader->load($this->filePath);
        $this->excel->setActiveSheetIndex(0);
        $this->sheet = $this->excel->getActiveSheet();
        
        
        /*$range = sprintf('A1:%s' . 7,
            \PHPExcel_Cell::stringFromColumnIndex($this->colsCount-1)
        );
        $this->cahcedRows = $this->sheet->rangeToArray($range);*/
        
        spl_autoload_register(array('YiiBase','autoload'));
    }
    
    
    ### Iterator methods ###
    
    /**
     * Rewind the Iterator to the first element
     */
    public function rewind() 
    {
        // Starts from 1
        $this->position = 1 + $this->firstRowPosition();
    }
    
    
    ### Xls reading methods ###
    
    
    /**
     * Returns row array with offsets as keys.
     * @return array integer => mixed
     */
    protected function getRow()
    {
        //return parent::getRow();
        
        // Try to return cached value
        if ($this->position == $this->lastRowPosition) {
            return $this->lastRow;
        }
            
        
        /*if (isset($this->cahcedRows[$this->position - 1])) {
            $row = $this->cahcedRows[$this->position - 1];
            
        } else {*/
            
            $range = sprintf('A%d:%s%1$d',
                $this->position,
                \PHPExcel_Cell::stringFromColumnIndex($this->colsCount-1)
            );
            $rows = $this->sheet->rangeToArray($range);
            $row = array_pop($rows);

            // Cache value
            $this->lastRowPosition = $this->position;
            $this->lastRow = $row;
        /*}*/
        
        return $row;
    }
    
    // See parent phpDoc
    protected function getItem($index)
    {
        $row = $this->getRow();
        return $row[$index];
        
        return $this->getCellValue($index, $this->position);
    }


    /**
     *
     * @param integer $col
     * @param integer $row 
     * @return \PHPExcel_Cell
     */
    protected function getCell($col, $row)
    {
        $coodrinate = \PHPExcel_Cell::stringFromColumnIndex($col) . $row;
        return $this->sheet->getCell($coodrinate);
    }
    
    /**
     *
     * @param integer $col
     * @param integer $row 
     * @return mixed
     */
    protected function getCellValue($col, $row)
    {
        return $this->getCell($col, $row)->getCalculatedValue();
    }
}

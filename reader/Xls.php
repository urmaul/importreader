<?php

namespace importreader\reader;

class Xls extends AbstractReader
{

    /**
     * @var \PHPExcel_Worksheet 
     */
    protected $sheet;


    public function init()
    {
        parent::init();
        
        $phpExcelPath = \Yii::getPathOfAlias('ext.phpexcel');
		spl_autoload_unregister(array('YiiBase','autoload'));
		require_once $phpExcelPath . DIRECTORY_SEPARATOR . 'PHPExcel.php';
		
		$objPHPExcel = \PHPExcel_IOFactory::load($this->filePath);
        $objPHPExcel->setActiveSheetIndex(0);
        $this->sheet = $objPHPExcel->getActiveSheet();
        spl_autoload_register(array('YiiBase','autoload'));
        
        /*$rowIndex = 0;
        $cellIndex = 0;
        
        $fileData = array();
        foreach($aSheet->getRowIterator() as $row)
        {
        	$cellIterator = $row->getCellIterator();
        	
        	foreach($cellIterator as $cell) 
        	{
        		$fileData[$rowIndex][$cellIndex] = $cell->getCalculatedValue();
        		$cellIndex++;
        	}
        	
        	$rowIndex++;
        	$cellIndex = 0;
        }*/
        
        $this->colsCount = count($this->labels);
    }
    
    
    ### Iterator methods ###
    
    /**
     * Rewind the Iterator to the first element
     */
    public function rewind() 
    {
        // Starts from 1 + 1 row for labels
        $this->position = 2;
    }
    
    /**
     * Checks if current position is valid
     * @return boolean
     */
    public function valid()
    {
        $val = $this->getCellValue(1, $this->position);
        return !empty($val);
    }
    
    
    ### Xls reading methods ###
    
    protected function getRow()
    {
        $row = array();
        
        $iRow = $this->position;
        
        for ($iCol=0; $iCol<$this->colsCount; ++$iCol) {
            $row[] = $this->getCellValue($iCol, $iRow);
        }
        
        return $row;
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

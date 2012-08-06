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
    protected $sheet;


    public function init()
    {
        parent::init();
        
        spl_autoload_unregister(array('YiiBase','autoload'));
		$path = \Yii::getPathOfAlias($this->phpExcelPathAlias . '.PHPExcel');
		require_once $path . '.php';
		
		$objPHPExcel = \PHPExcel_IOFactory::load($this->filePath);
        $objPHPExcel->setActiveSheetIndex(0);
        $this->sheet = $objPHPExcel->getActiveSheet();
        spl_autoload_register(array('YiiBase','autoload'));
        
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
    
    // See parent phpDoc
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

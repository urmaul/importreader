<?php

namespace importreader;

class Reader implements \Iterator
{
    /**
     * Input file path
     * @var string 
     */
    public $filePath;

    /**
     * Count of rows at start that reader will ignore
     * @var integer
     */
    public $ignoredRowsCount = 0;

    /**
     * True if you want to use string labels as row items keys.
     * False if you want to use integer indexes as row items keys.
     * @var boolean
     */
    public $useLabels = false;
    
    /**
     * Labels array.
     * You may use empty value as label if you want to ignote that field.
     * @var array integer => string
     */
    public $labels;
    
    /**
     * Callback function to prepare row before returning
     * @var callable
     */
    public $callback;
    
    /**
     * Count of columns we need to read.
     * This value will be overwritten if you set "useLabels" property to true.
     * @var integer
     */
    public $colsCount;
    
    
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

    protected $position = 0;
    
    public function init()
    {
        $reader = \PHPExcel_IOFactory::createReaderForFile($this->filePath);
        /* @var $reader \PHPExcel_Reader_IReader */
        
        $reader->setReadDataOnly(true);
        
        // Load only 1st sheet
        if (method_exists($reader, 'listWorksheetNames')) {
            $sheets = $reader->listWorksheetNames($this->filePath);
            $reader->setLoadSheetsOnly( array($sheets[0]) );
        }
        
        $this->reader = $reader;
        
        $this->excel = $this->reader->load($this->filePath);
        $this->excel->setActiveSheetIndex(0);
        $this->sheet = $this->excel->getActiveSheet();
        
        if ($this->useLabels) {
			if ($this->labels === null) {
				$this->labels = $this->readLabelsRow();
				$this->ignoredRowsCount++; // To pass labels row when reading data
			}
            $this->colsCount = count($this->labels);
        }
        
        if ($this->colsCount === null)
			throw new Exception(__CLASS__.'->colsCount not set.');
		
        $this->filter = new Filter($this->colsCount);
        $reader->setReadFilter($this->filter);
    }
    
    public function readAll()
    {
		if (!$this->reader)
			$this->init();
		
		$rows = array();
		
		foreach ($this as $row) {
			$rows[] = $row;
		}
		
		return $rows;
	}
    
    ### Iterator methods ###
    
    /**
     * Rewind the Iterator to the first element
     */
    public function rewind() 
    {
        // Starts from 1
        $this->position = 1 + $this->firstRowPosition();
        //$this->position = $this->firstRowPosition();
        return $this->current();
    }

    /**
     * Returns row array with labels as keys.
     * Row is updated using callback.
     * @return array label => value
     */
    public function current()
    {
        if ($this->valid())
            return $this->useCallback( $this->getLabeledRow() );
        else
            return null;
    }
    
    /**
     * Return the key of the current element
     */
    public function key() 
    {
        return $this->position;
    }

    /**
     * Move forward to next element
     */
    public function next() 
    {
        ++$this->position;
        return $this->current();
    }

    /**
     * Checks if current position is valid
     * @return boolean
     */
    public function valid()
    {
        $item = $this->getItem(0);
        return !empty($item);
    }
    
    
    ### Reading methods ###
    
    protected function firstRowPosition()
    {
        return $this->ignoredRowsCount;
    }
    
    protected function readLabelsRow()
    {
		$labels = array();
		
		$row = 1;
		$col = 0;
		do {
			$label = $this->getCellValue($col, $row);
			
			if (!empty($label))
				$labels[] = $label;
			
			$col++;
		} while (!empty($label));
		
		return $labels;
	}
    
    /**
     * Returns row array with offsets as keys.
     * @return array integer => mixed
     */
    protected function getRow()
    {
        // Try to return cached value
        if ($this->position == $this->lastRowPosition) {
            return $this->lastRow;
        }
        
        $range = sprintf('A%d:%s%1$d',
            $this->position,
            \PHPExcel_Cell::stringFromColumnIndex($this->colsCount-1)
        );
        $rows = $this->sheet->rangeToArray($range);
        $row = array_pop($rows);

        // Cache value
        $this->lastRowPosition = $this->position;
        $this->lastRow = $row;
        
        return $row;
    }
    
    // See parent phpDoc
    protected function getItem($index)
    {
        $row = $this->getRow();
        return $row[$index];
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
    
    /**
     * Returns row array with labels as keys.
     * @return array string => mixed
     */
    protected function getLabeledRow()
    {
        if ($this->useLabels) {
            $labels = $this->labels;
            $values = $this->getRow();

            $row = array_combine($labels, $values);

            unset($row['']); // Remove values with null labels

            return $row;
            
        } else
            return $this->getRow();
    }
    
    /**
     * Runs callbask if it is registered
     * @param array $row labeled row
     * @return array updated labeled row
     */
    protected function useCallback($row)
    {
        if (is_callable($this->callback))
            return call_user_func($this->callback, $row);
        else
            return $row;
    }
}

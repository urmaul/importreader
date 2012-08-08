<?php

namespace importreader\reader;

abstract class AbstractReader extends \CComponent implements \Iterator
{
    /**
     * Input file path
     * @var string 
     */
    public $filePath;

    /**
     * True if you want to use string labels as row items keys.
     * False if you want to use integer indexes as row items keys.
     * @var boolean
     */
    public $useLabels = true;
    
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
    protected $colsCount = 1;

    
    protected $position = 0;
    
    public function init()
    {
        if ($this->useLabels) {
            $this->colsCount = count($this->labels);
        }
    }
    
    ### Iterator methods ###
    
    /**
     * Rewind the Iterator to the first element
     */
    public function rewind() 
    {
        $this->position = 0;
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
    
    /**
     * Returns row array with offsets as keys.
     * @return array integer => mixed
     */
    protected function getRow()
    {
        $row = array();
        
        for ($index=0; $index<$this->colsCount; ++$index) {
            $row[] = $this->getItem($index);
        }
        
        return $row;
    }
    
    /**
     * Returns current row item with given index.
     * You need 
     * @param integer $index
     * @return mixed item value
     */
    protected function getItem($index)
    {
        throw new \CException(__METHOD__ . ' is not overridden and called');
        
        array($index); // Just to get rid of warning
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

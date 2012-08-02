<?php

namespace importreader\reader;

abstract class AbstractReader extends \CComponent implements \Iterator
{
    public $filePath;

    public $labels;
    
    public $callback;


    protected $colsCount;

    protected $position = 0;
    
    public function init()
    {
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
     * Return the current element
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
    #abstract public function valid();
    
    
    ### Reading methods ###
    
    abstract protected function getRow();
    
    protected function getLabeledRow()
    {
        $labels = $this->labels;
        $values = $this->getRow();
        
        $row = array_combine($labels, $values);
        
        unset($row['']); // Remove values with null labels
        
        return $row;
    }
    
    protected function useCallback($row)
    {
        if (is_callable($this->callback))
            return call_user_func($this->callback, $row);
        else
            return $row;
    }
}

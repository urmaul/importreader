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
    #abstract public function valid();
    
    
    ### Reading methods ###
    
    /**
     * Returns row array with offsets as keys.
     * @return array integer => mixed
     */
    abstract protected function getRow();
    
    /**
     * Returns row array with labels as keys.
     * @return array string => mixed
     */
    protected function getLabeledRow()
    {
        $labels = $this->labels;
        $values = $this->getRow();
        
        $row = array_combine($labels, $values);
        
        unset($row['']); // Remove values with null labels
        
        return $row;
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

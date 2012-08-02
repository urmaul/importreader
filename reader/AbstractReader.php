<?php

namespace importreader\reader;

abstract class AbstractReader extends \CComponent implements \Iterator
{
    public $filepath;

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
        $this->_position = 0;
    }

    /**
     * Return the current element 
     */
    #abstract public function current();

    /**
     * Return the key of the current element
     */
    public function key() 
    {
        return $this->_position;
    }

    /**
     * Move forward to next element
     */
    public function next() 
    {
        ++$this->_position;
    }

    /**
     * Checks if current position is valid
     */
    #abstract public function valid();
}

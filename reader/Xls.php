<?php

namespace importreader\reader;

class Xls extends AbstractReader
{
    public function current()
    {
        return null;
    }
    
    public function valid()
    {
        return false;
    }
}

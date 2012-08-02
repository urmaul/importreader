<?php

namespace importreader;

if (!class_exists('\importreader\Component')) {

class Component extends \CApplicationComponent
{
    /**
     * @var array extension => class 
     */
    protected $readersMap = array(
        'xls' => 'importreader\\reader\\Xls',
        'xlsx' => 'importreader\\reader\\Xls',
    );

    /**
     * Creates and inits file reader instance
     * @param string $filepath
     * @return reader\AbstractReader
     * @throws \CException 
     */
    public function makeReader($filepath)
    {
        $extenstion = pathinfo($filepath, PATHINFO_EXTENSION);
        
        if ( !isset($this->readersMap[$extenstion]) ) {
            throw new \CException('No reader for extenstion "' . $extenstion . '"');
        }
        
        $class = $this->readersMap[$extenstion];
        
        $reader = \Yii::createComponent(array(
            'class' => $class,
            'filepath' => $filepath,
        ));
        
        return $reader;
    }
}

}

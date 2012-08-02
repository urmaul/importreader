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
    public function makeReader($filePath, $params = array())
    {
        $extenstion = pathinfo($filePath, PATHINFO_EXTENSION);
        
        if ( !isset($this->readersMap[$extenstion]) ) {
            throw new \CException('No reader for extenstion "' . $extenstion . '"');
        }
        
        $class = $this->readersMap[$extenstion];
        
        $params = array_merge(array(
            'class' => $class,
            'filePath' => $filePath,
        ), $params);
        
        $reader = \Yii::createComponent($params);
        $reader->init();
        return $reader;
    }
}

}

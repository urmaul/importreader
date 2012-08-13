ImportReader
============

Yii Import Table files reader

Requirements
------------
* PHP 5.3.0
* Yii 1.1.5
* PHPExcel library (https://github.com/PHPOffice/PHPExcel)

How to attach
-------------

Add "importreader" namespace to Yii import aliases.

```php
// in controller
Yii::setPathOfAlias('importreader', Yii::getPathOfAlias('ext.importreader'));

// or

// in config
Yii::setPathOfAlias('importreader', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'importreader');
```

Register importReader application component.

```php
'importReader' => array(
    'class' => '\importreader\Component',
),
```

How to use
----------

```php
$reader = Yii::app()->importReader->makeReader('file.xml', array(
    // See reader class public properties. You may set them here.
));

foreach ($reader as $row) {
    // Use $row array here
}

```

TODO
----

* More documentation
* Ability to parse first row as labels array
* PHPExcel attaching configuration
* CSV reader
* Include PHPExcel into extension

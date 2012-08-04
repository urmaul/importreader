importreader
============

Yii Import Table files reader

Requirements
------------
* PHP 5.3.0
* Yii 1.1.5

How to attach
-------------

```php
// in controller
Yii::setPathOfAlias('importreader', Yii::getPathOfAlias('ext.importreader'));

// or

// in config
Yii::setPathOfAlias('importreader', dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'extensions'.DIRECTORY_SEPARATOR.'importreader');
```


```php
'importReader' => array(
    'class' => '\importreader\Component',
),
```

TODO
----

* More documentation
* Ability to parse first row as labels array
* PHPExcel attaching configuration
* CSV reader

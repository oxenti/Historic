# Historic Behavior for cakephp

This add Behavior de historic for table

## Requirements

* CakePHP 3.0+

## Installation

You can install this behavior into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```sh
composer require oxenti/historic
```

## Configuration

In your app's `config/bootstrap.php` add:

```php
// In config/bootstrap.php
Plugin::load('Historic');
```

or using cake's console:

```sh
./bin/cake plugin load Historic
```

On your app's,model  add the Histotic Behevior in initialize method:
```php
    ...
     $this->addBehavior('Historic.Historic'[
        'class' => 'Historic`s table'
        'fields' => ['fields']//fields that should be saved on historic table
    ]);
    ...
```

Warning: Historic's table require field is_active tyniInt(1).


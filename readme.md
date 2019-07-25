# Nova Simple Cms

## WORK IN PROGRESS! THIS PACKAGE MIGHT NOT WORK AS EXCEPTED OR AT ALL

## Description

This package makes it easy to create pages that are editable in Nova.

## Installation

You can install the package in to a Laravel app that uses Nova via composer:
```
composer require ninjami/nova-simple-cms
```

```
// in app/Providers/NovaServiceProvder.php

// ...
public function tools()
{
    return [
        // ...

        new \Ninjami\NovaSimpleCms\NovaSimpleCms()
}
``` 

Publish migrations and migrate:
```
php artisan vendor:publish --tag=migrations --provider=Ninjami\\NovaSimpleCms\\ToolServiceProvider
php artisan migrate
```

Publish config:
```
php artisan vendor:publish --tag=config --provider=Ninjami\\NovaSimpleCms\\ToolServiceProvider
```

Create blueprints folder and copy example blueprint:
```
php artisan vendor:publish --tag=blueprints --provider=Ninjami\\NovaSimpleCms\\ToolServiceProvider
```
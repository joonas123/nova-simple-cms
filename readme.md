# Nova Simple Cms

## WORK IN PROGRESS! THIS PACKAGE MIGHT NOT WORK AS EXCEPTED OR AT ALL

## Description

This package makes it easy to create pages that are editable in Nova.

## Installation

You can install the package in to a Laravel app that uses Nova via composer:
```
composer require joonas1234/nova-simple-cms
```

Add package to NovaServiceProvider:
```
// in app/Providers/NovaServiceProvder.php

// ...
public function tools()
{
    return [
        // ...

        new \Joonas1234\NovaSimpleCms\NovaSimpleCms()
}
``` 

Publish migrations, config and example
```
php artisan vendor:publish --provider=Joonas1234\\NovaSimpleCms\\ToolServiceProvider
```

Publish only migrations and migrate:
```
php artisan vendor:publish --tag=migrations --provider=Joonas1234\\NovaSimpleCms\\ToolServiceProvider
```

If you publish page migrations, remember to run:
```
php artisan migrate
```

Publish only config:
```
php artisan vendor:publish --tag=config --provider=Joonas1234\\NovaSimpleCms\\ToolServiceProvider
```

Publish example template
```
php artisan vendor:publish --tag=example --provider=Joonas1234\\NovaSimpleCms\\ToolServiceProvider
```

### Froala field
This package currently uses Froala for content -field. https://github.com/froala/nova-froala-field

You have to publish and run Froala migrations to make it work:
```
php artisan vendor:publish --tag=migrations --provider=Froala\\NovaFroalaField\\FroalaFieldServiceProvider 
php artisan migrate
```
# Nova Simple Cms

## WORK IN PROGRESS! THIS PACKAGE MIGHT NOT WORK AS EXCEPTED OR AT ALL

## Description

This package adds powerful but simple CMS to your Nova. You only need to add blueprint and template with same name and then you can start adding pages. Everything else is already handled! 

## Installation

```
composer require joonas1234/nova-simple-cms
```

Then register the tool :
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
You can publish configuration file and migrations with command: `php artisan vendor:publish`


To publish only migrations, run:
```
php artisan vendor:publish --tag=migrations --provider=Joonas1234\\NovaSimpleCms\\ToolServiceProvider
```

Then you have to run migrate to create `pages` -table
```
php artisan migrate
```

To publish only config:
```
php artisan vendor:publish --tag=config --provider=Joonas1234\\NovaSimpleCms\\ToolServiceProvider
```

To publish only template example
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

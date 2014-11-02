# Laravel localisation

A bridge package for providing Laravel specific components and implementations of the Localisation package.

## Installation

Install via composer:

    composer require tectonic/laravel-localisation

## Usage

As we're dealing with Laravel 4/5, we get some nice ways of dealing with the Localisation package, as well
as the various transformers that this package also provides. First, make sure you add the service provider to
your app/config/app.php file, in the providers array:

    'Tectonic\LaravelLocalisation\ServiceProvider'

Now that our service provider is added, we can start using localisation within our package and on our models and collections.

The package's ServiceProvider automatically registers a facade and alias for us, known simply as Translator. This allows us to register extra transformers should we need, as well as provide a nice helper method for doing translations:

```php
<?php
$content = Content::find(1);

Translator::translate($content);
```

The translator will decorate the $content object with the fields that have been translated. These fields can be found on the $content->getTranslated() method. It's important to note that ALL translations will be available via this method. Should you wish to restrict that to say, just the user's current locale, do the following:
 
     Translator::translate($content, 'en_GB');
 
 This will return the content object with only the en_GB translations attached.
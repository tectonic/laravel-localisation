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
 
 It should be noted that the translator and transformers do not actually (and should not) change the model's fields. This pattern is used and designed from the perspective of users being able to change the languages and translations available to them via some sort of UI implementation - not via Laravel config files (which is already handled by Laravel for you).
 
 As a result, models (resources) should not be designed with the view that their properties will change. For example, say you have a content model and it has a title and description that you want to have multi-lingual support for. The content model should not, in fact - actually have these properties defined. These are fields that can be populated via the Translation model and associated table.
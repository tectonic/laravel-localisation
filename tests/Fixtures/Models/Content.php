<?php

namespace Tests\Fixtures\Models;

use Tectonic\LaravelLocalisation\Database\TranslationRetriever;
use Tectonic\Localisation\Contracts\Translatable;
use Tectonic\Localisation\Translator\Translations;

class Content extends \Eloquent implements Translatable
{
    use TranslationRetriever;
    use Translations;

    public ?string $preferredLanguage = null;

    public function setTranslationLanguage(?string $language): void
    {
        $this->preferredLanguage = $language;
    }

    public $table = 'content';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function links()
    {
        return $this->hasMany(Link::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Returns an array of the field names that can be used for translations.
     *
     * @return array
     */
    public function getTranslatableFields()
    {
        return ['title', 'description'];
    }
}

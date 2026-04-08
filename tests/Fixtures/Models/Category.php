<?php

namespace Tests\Fixtures\Models;

use Tectonic\LaravelLocalisation\Database\TranslationRetriever;
use Tectonic\Localisation\Contracts\Translatable;
use Tectonic\Localisation\Translator\Translations;

class Category extends \Eloquent implements Translatable
{
    use TranslationRetriever;
    use Translations;

    public ?string $preferredLanguage = null;

    public function setTranslationLanguage(?string $language): void
    {
        $this->preferredLanguage = $language;
    }

    public function content()
    {
        return $this->hasMany(Content::class);
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

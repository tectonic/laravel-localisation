<?php
namespace Tests\Fixtures\Models;

use Tectonic\LaravelLocalisation\Database\TranslationRetriever;
use Tectonic\Localisation\Contracts\Translatable;
use Tectonic\Localisation\Translator\Translations;

class Author extends \Eloquent implements Translatable
{
    use Translations;
    use TranslationRetriever;

	public function content()
    {
        return $this->hasMany(Content::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Returns an array of the field names that can be used for translations.
     *
     * @return array
     */
    public function getTranslatableFields()
    {
        return ['summary'];
    }
}

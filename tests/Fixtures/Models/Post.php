<?php
namespace Tests\Fixtures\Models;

use Tectonic\LaravelLocalisation\Database\TranslationRetriever;
use Tectonic\Localisation\Contracts\Translatable;
use Tectonic\Localisation\Translator\Translations;

class Post extends \Eloquent implements Translatable
{
    use Translations;
    use TranslationRetriever;

	public function category()
    {
        return $this->belongsTo(Category::class);
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

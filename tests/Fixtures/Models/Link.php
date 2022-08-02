<?php
namespace Tests\Fixtures\Models;

use Tectonic\LaravelLocalisation\Database\TranslationRetriever;
use Tectonic\Localisation\Contracts\Translatable;
use Tectonic\Localisation\Translator\Translations;

class Link extends \Eloquent implements Translatable
{
    use Translations;
    use TranslationRetriever;

	public function content()
    {
        return $this->belongsTo(Content::class);
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

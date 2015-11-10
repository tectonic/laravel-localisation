<?php
namespace Tests\Fixtures\Models;

use Tectonic\Localisation\Contracts\Translatable;
use Tectonic\Localisation\Translator\Translations;

class Content extends \Eloquent implements Translatable
{
    use Translations;

    public $table = 'content';

	public function category()
    {
        return $this->belongsTo(Category::class);
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

<?php
namespace Tests\Fixtures\Models;

use Tectonic\Localisation\Translator\Translatable;

class Content extends \Eloquent
{
    use Translatable;

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

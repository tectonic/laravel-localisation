<?php
namespace Tests\Fixtures\Models;

use Tectonic\Localisation\Translator\Translatable;

class Category extends \Eloquent
{
    use Translatable;

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

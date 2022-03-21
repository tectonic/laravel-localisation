<?php
namespace Tests\Stubs;

use Tectonic\Localisation\Contracts\Translatable;
use Tectonic\Localisation\Translator\Translations;

class Model implements Translatable
{
	use Translations;

    public $id = 1;

    /**
     * Returns an array of the field names that can be used for translations.
     *
     * @return array
     */
    public function getTranslatableFields()
    {
        return ['name'];
    }
    
    public function touch()
    {
    }
}

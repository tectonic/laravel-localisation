<?php
namespace tests\Stubs;

use Tectonic\Localisation\Contracts\TranslatableInterface;
use Tectonic\Localisation\Translator\Translatable;

class Model implements TranslatableInterface
{
	use Translatable;

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
}

<?php
namespace Tectonic\LaravelLocalisation\Translator;

class Engine extends \Tectonic\Localisation\Translator\Engine
{

    /**
     * Alias for translate
     *
     * @param $object
     * @param  null  $language
     * @return mixed|object
     */
    public function get($object, $language = null)
    {
        return parent::translate($object, $language);
    }
}

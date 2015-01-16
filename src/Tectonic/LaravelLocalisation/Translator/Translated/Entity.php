<?php
namespace Tectonic\LaravelLocalisation\Translator\Translated;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Entity implements Arrayable, Jsonable
{
    /**
     * Stores all the translations, grouped by language.
     *
     * @var array
     */
    private $translations = [];

    /**
     * Construct the entity with the required attributes.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setAttributes($attributes);
    }

    /**
     * Applies a given translation for a language, field and its value.
     *
     * @param $languageCode
     * @param $field
     * @param $value
     */
    public function applyTranslation($languageCode, $field, $value)
    {
        $this->addTranslatedField($languageCode, $field, $value);
        $this->addLanguageFieldValue($languageCode, $field, $value);
    }

    /**
     * Add the translated field to the entity.
     *
     * @param $languageCode
     * @param $field
     * @param $value
     */
    private function addTranslatedField($languageCode, $field, $value)
    {
        if (!isset($this->$field)) {
            $this->$field = [];
        }

        $this->{$field}[$languageCode] = $value;
    }

    /**
     * Add the translated field to the language attribute on the entity. This provides a nice method
     * to be able to pull in all the translations for a given language, rather than searching
     * through each field one-by-one.
     *
     * @param $languageCode
     * @param $field
     * @param $value
     */
    private function addLanguageFieldValue($languageCode, $field, $value)
    {
        if (!isset($this->translations[$languageCode])) {
            $this->translations[$languageCode] = [];
        }

        $this->translations[$languageCode][$field] = $value;
    }

    /**
     * Returns all the translations for each language and field saved to the database.
     *
     * @return array
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Set the attributes on the entity.
     *
     * @param $attributes
     */
    private function setAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            // Translations is protected
            if ($key == 'translations') continue;

            $this->$key = $value;
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = get_object_vars($this);

        foreach ($array as &$item) {
            if ($item instanceof Collection) {
                $item = $item->toArray();
            }
        }

        return $array;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_decode($this->toArray(), $options);
    }
}

<?php
namespace Tectonic\LaravelLocalisation\Translator\Translated;

class Entity
{
    /**
     * Stores the values, translations.etc. for a given entity.
     *
     * @var array
     */
    private $attributes = [];

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
        $this->attributes = $attributes;
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

        $this->$field[$languageCode] = $value;
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
     * Set an attribute.
     *
     * @param $attribute
     * @param $value
     */
    public function __set($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * Get an attribute.
     *
     * @param $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        return $this->attributes[$attribute];
    }

    /**
     * Check to see if an attribute is set.
     *
     * @param $attribute
     * @return bool
     */
    public function __isset($attribute)
    {
        return isset($this->attributes[$attribute]);
    }
}

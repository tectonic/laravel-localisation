<?php
namespace Tectonic\LaravelLocalisation\Database;

use Illuminate\Support\Facades\App;

trait TranslationRetriever
{
    /**
     * Return the translation for a given language and key.
     *
     * @param string $lang
     * @param string $key
     * @return mixed
     */
    public function trans($lang, $key)
    {
        if (isset($this->translated[$lang][$key])) {
            return $this->translated[$lang][$key];
        }
    }

    /**
     * Returns the language translation for the given key, based on the app's current
     * locale setting.
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        $lang = App::getLocale();

        if (isset($this->translated[$lang][$key])) {
            return $this->trans($lang, $key);
        }

        return parent::__get($key);
    }

    /**
     * Returns all translations available to the model.
     * 
     * @return mixed
     */
    public function getTranslations()
    {
        return $this->translated;
    }
}

<?php
namespace Tectonic\LaravelLocalisation\Translator\Transformers;

use Tectonic\LaravelLocalisation\Translator\Translated\Collection as TranslatedCollection;
use Illuminate\Database\Eloquent\Collection;
use Tectonic\Localisation\Contracts\TransformerInterface;
use Tectonic\Localisation\Translator\Transformers\Transformer;

class CollectionTransformer extends Transformer implements TransformerInterface
{
    /**
     * This transformer is only appropriate for eloquent collection objects.
     *
     * @param $object
     * @return mixed
     */
    public function isAppropriateFor($object)
    {
        return $object instanceof Collection;
    }

    /**
     * Once a transformer for an object has been found, it then must do whatever work is necessary on that object.
     *
     * @param Collection $collection
     * @return mixed
     */
    public function transform($collection)
    {
        return $this->translate($collection, false);
    }

    /**
     * Same as transform but should only translate objects one-level deep. With collections, we always
     * have to pass off to the model transformer anyway, so just simply re-call the transform method.
     *
     * @param object $collection
     * @return mixed
     */
    public function shallow($collection)
    {
        return $this->translate($collection, true);
    }

    /**
     * Translates the collection.
     *
     * @param $collection
     * @param boolean $shallow
     * @return TranslatedCollection
     */
    private function translate($collection, $shallow)
    {
        $resources = $this->getTranslationResources($collection, $shallow);
        $translations = $this->getTranslations($resources);

        return $this->applyTranslations($collection, $translations, $shallow);
    }

    /**
     * Gets the resources and their associated IDs that will be needed for translation later.
     *
     * @param Collection $collection
     * @param bool $shallow
     * @return array
     */
    public function getTranslationResources(Collection $collection, $shallow)
    {
        $resources = [];

        foreach ($collection as $model) {
            $modelResources = app(ModelTransformer::class)->getTranslationResources($model, $shallow);
            $resources = $this->mergeResources($resources, $modelResources);
        }

        return $resources;
    }

    /**
     * Applies the given translations to the collection.
     *
     * @param Collection $collection
     * @param Collection $translations
     * @param bool $shallow
     * @return TranslatedCollection
     */
    public function applyTranslations(Collection $collection, Collection $translations, $shallow)
    {
        foreach ($collection as $model) {
            app(ModelTransformer::class)->applyTranslations($model, $translations, $shallow);
        }

        return $collection;
    }
}

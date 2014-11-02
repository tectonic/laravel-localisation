<?php
namespace Tectonic\LaravelLocalisation\Translator\Transformers;

use Illuminate\Database\Eloquent\Collection;
use Tectonic\Localisation\Contracts\TransformerInterface;

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
        $resources = $this->getTranslationResources($collection);
        $translations = $this->getTranslations($resources);

        $this->applyTranslations($collection, $translations);
    }

    /**
     * Gets the resources and their associated IDs that will be needed for translation later.
     *
     * @param $collection
     * @return array
     */
    public function getTranslationResources($collection)
    {
        $resources = [];

        foreach ($collection as $model) {
            $modelResources = with(new ModelTransformer)->getTranslationResources($model);
            $resources = $this->mergeResources($resources, $modelResources);
        }

        return $resources;
    }

    /**
     * Applies the given translations to the collection.
     *
     * @param Collection $collection
     * @param Collection $translations
     */
    public function applyTranslations(Collection $collection, Collection $translations)
    {
        foreach ($collection as $model) {
            with(new ModelTransformer)->applyTranslations($model, $translations);
        }
    }
}

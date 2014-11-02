<?php
namespace Tectonic\Shift\Modules\Localisation\Translator\Transformers;

use Eloquence\Database\Model;
use Illuminate\Database\Eloquent\Collection;
use Tectonic\Shift\Modules\Localisation\Contracts\TransformerInterface;

class ModelTransformer implements TransformerInterface
{
    /**
     * Implementations should take an object as a parameter, and then respond with a boolean
     * true or false depending on whether or not they are able to transform that object.
     *
     * @param $object
     * @return mixed
     */
    public function isAppropriateFor($object)
    {
        return $object instanceof Model;
    }

    /**
     * Once a transformer for an object has been found, it then must do whatever work is necessary on that object.
     *
     * @param Model $model
     * @return mixed
     */
    public function transform($model)
    {
        $resources = $this->getTranslationResources($model);
        $translations = $this->getTranslations($resources);

        $this->applyTranslations($model, $translations);
    }

    /**
     * Returns an array containing the model's resource, and then a child array that contains the model's id.
     *
     * @param $model
     * @return array
     */
    public function getTranslationResources(Model $model)
    {
        // If the model is not translatable, do nothing
        if (!($model instanceof Translatable)) return;

        $resources = [class_basename($model) => [$model->id]];

        // Now loop through each of the eagerly loaded relations, and get the resources from them as well
        foreach ($model->getRelations() as $relation) {
            $collectionResources = with(new CollectionTransformer)->getTranslationResources($relation);
            $resources = $this->mergeResources($resources, $collectionResources);
        }
    }

    /**
     * Applies the found translations to a given model. Loops through each of the provided resources,
     * once one matches the given model's class, it will then apply those fields and values for its
     * record. It will then break the loop.
     *
     * @param Model $model
     * @param Collection $collection
     */
    public function applyTranslations(Model $model, Collection $translations)
    {
        // If the model is not translatable, do nothing
        if (!($model instanceof Translatable)) return;

        foreach ($translations as $translation) {
            if (!($translation->resource == class_basename($model) && $translation->foreignId == $model->id)) continue;

            $model->applyTranslation($translation->language->code, $translation->field, $translation->value);
        }

        // Now we apply to the translations to each o the model's eagerly-loaded relationships
        foreach ($model->getRelations() as $collection) {
            with(new CollectionTransformer)->applyTranslations($collection, $translations);
        }
    }
}
 
<?php
namespace Tectonic\LaravelLocalisation\Translator\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Tectonic\Localisation\Contracts\TransformerInterface;
use Tectonic\LaravelLocalisation\Translator\Translated\Entity;
use Tectonic\Localisation\Translator\Transformers\Transformer;
use Tectonic\Localisation\Translator\Translatable;

class ModelTransformer extends Transformer implements TransformerInterface
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

        return $this->applyTranslations($model, $translations);
    }

    /**
     * Returns an array containing the model's resource, and then a child array that contains the model's id.
     *
     * @param $model
     * @return array
     */
    public function getTranslationResources(Model $model)
    {
        $resources = $this->baseResources($model);

        // Now loop through each of the eagerly loaded relations, and get the resources from them as well
        foreach ($model->getRelations() as $item) {
            $newResources = $this->resolveTransformer($item)->getTranslationResources($item);
            $resources = $this->mergeResources($resources, $newResources);
        }

        return $resources;
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
        $entity = new Entity($model->getAttributes());

        if (!$this->isTranslatable($model)) {
            return $entity;
        }

        foreach ($translations as $translation) {
            if (!($translation->resource == class_basename($model) && $translation->foreign_id == $model->id)) continue;

            $entity->applyTranslation($translation->language, $translation->field, $translation->value);
        }

        // Now we apply to the translations to each o the model's eagerly-loaded relationships
        foreach ($model->getRelations() as $relationship => $item) {
            $entity->$relationship = $this->resolveTransformer($item)->applyTranslations($item, $translations);
        }

        return $entity;
    }

    /**
     * Pulls in the base resources for the model. If the model is translatable, it will fetch
     * the translatable resource name (the base class name) and then use this as the key for the
     * array of ids that will be used to search for translations
     *
     * @param $model
     * @return array
     */
    private function baseResources($model)
    {
        if ($this->isTranslatable($model)) {
            return [$model->getResourceName() => [$model->id]];
        }

        return [];
    }

    private function resolveTransformer($item)
    {
        if ($item instanceof Collection) {
            return new CollectionTransformer;
        }

        if ($item instanceof Model) {
            return new ModelTransformer;
        }
    }

    /**
     * Determines whether or not a model has translatable properties.
     *
     * @param Model $model
     * @return bool
     */
    private function isTranslatable(Model $model)
    {
        return in_array(Translatable::class, class_uses($model));
    }
}
 
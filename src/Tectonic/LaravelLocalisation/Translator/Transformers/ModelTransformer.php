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
        return $this->translate($model, false);
    }

    /**
     * Same as transform but should only translate objects one-level deep.
     *
     * @param object $model
     * @return mixed
     */
    public function shallow($model)
    {
        return $this->translate($model, true);
    }

    /**
     * Translates the model and returns the resulting transformed entity.
     *
     * @param Model $model
     * @param boolean $shallow
     * @return Entity
     */
    public function translate($model, $shallow)
    {
        $resources = $this->getTranslationResources($model, $shallow);
        $translations = $this->getTranslations($resources);

        return $this->applyTranslations($model, $translations, $shallow);
    }

    /**
     * Returns an array containing the model's resource, and then a child array that contains the model's id.
     *
     * @param Model $model
     * @param bool $shallow
     * @return array
     * @throws \Exception
     */
    public function getTranslationResources(Model $model, $shallow = false)
    {
        $resources = $this->baseResources($model);

        if ($shallow) return $resources;

        // Now loop through each of the eagerly loaded relations, and get the resources from them as well
        foreach ($model->getRelations() as $item) {
            // Some relationships can result in null values, so skip over those
            if (is_null($item)) {
                continue;
            }

            $newResources = $this->resolveTransformer($item)->getTranslationResources($item, $shallow);
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
     * @param Collection $translations
     * @param bool $shallow
     * @return Entity
     * @throws \Exception
     */
    public function applyTranslations(Model $model, Collection $translations, $shallow)
    {
        $entity = new Entity($model->getAttributes());

        // Loop through each oft he available translations and apply it to the entity
        foreach ($translations as $translation) {
            if (!($translation->resource == class_basename($model) && $translation->foreign_id == $model->id)) continue;

            $entity->applyTranslation($translation->language, $translation->field, $translation->value);
        }

        if ($shallow) return $entity;

        // Now we apply to the translations to each of the model's eagerly-loaded relationships
        foreach ($model->getRelations() as $relationship => $item) {
            if (is_null($item)) {
                $entity->$relationship = null;
            }
            else {
                $entity->$relationship = $this->resolveTransformer($item)->applyTranslations($item, $translations, $shallow);
            }
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

    /**
     * Deciphers the transformer that can be used for the $item in question.
     *
     * @param $item
     * @return CollectionTransformer|ModelTransformer
     * @throws \Exception
     */
    private function resolveTransformer($item)
    {
        if ($item instanceof Collection) {
            return app(CollectionTransformer::class);
        }

        if ($item instanceof Model) {
            return app(ModelTransformer::class);
        }

        throw new \Exception("No transformer found for {get_class($item)}.");
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

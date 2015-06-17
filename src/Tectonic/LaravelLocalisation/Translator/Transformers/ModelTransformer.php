<?php
namespace Tectonic\LaravelLocalisation\Translator\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SimpleCollection;
use Tectonic\Localisation\Contracts\TransformerInterface;
use Tectonic\LaravelLocalisation\Translator\Translated\Entity;
use Tectonic\Localisation\Translator\Transformers\Transformer;
use Tectonic\Localisation\Translator\Translatable;

class ModelTransformer extends Transformer implements TransformerInterface
{
    /**
     * @var SimpleCollection
     */
    private static $cache;

    public function __construct()
    {
        self::$cache = self::$cache ?: new SimpleCollection;
    }

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
            // Some relationships can result in null values, so skip over those
            if (is_null($item)) {
                continue;
            }

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
     * @param Model      $model
     * @param Collection $translations
     * @return Entity
     */
    public function applyTranslations(Model $model, Collection $translations)
    {
        if ($this->isCached($model)) {
            return $this->getCached($model);
        }

        $entity = new Entity($model->getAttributes());

        foreach ($translations as $translation) {
            if (!($translation->resource == class_basename($model) && $translation->foreign_id == $model->id)) continue;

            $entity->applyTranslation($translation->language, $translation->field, $translation->value);
        }

        // Now we apply to the translations to each o the model's eagerly-loaded relationships
        foreach ($model->getRelations() as $relationship => $item) {
            if (is_null($item)) {
                $entity->$relationship = null;
            }
            else {
                $entity->$relationship = $this->resolveTransformer($item)->applyTranslations($item, $translations);
            }
        }

        $this->putCached($model, $entity);

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
     */
    private function resolveTransformer($item)
    {
        if ($item instanceof Collection) {
            return new CollectionTransformer;
        }

        if ($item instanceof Model) {
            return new ModelTransformer;
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

    /**
     * Checks if the provided model's translations has been cached or not.
     *
     * @param Model $model
     * @return bool
     */
    private function isCached(Model $model)
    {
        if (!method_exists($model, 'getTranslationCacheKey')) {
            return false;
        }

        return self::$cache->has($model->getTranslationCacheKey());
    }

    /**
     * Retrieves the cached entity from the cache repository, given the model cache key.
     *
     * @param Model  $model
     * @param mixed  $default
     * @return Entity
     */
    private function getCached(Model $model, $default = null)
    {
        if (!method_exists($model, 'getTranslationCacheKey')) {
            return false;
        }

        return self::$cache->get($model->getTranslationCacheKey(), $default);
    }

    /**
     * Caches the given Entity under the Model key, so it can be retrieved later.
     *
     * @param Model  $model
     * @param Entity $entity
     */
    private function putCached(Model $model, Entity $entity)
    {
        if (!method_exists($model, 'getTranslationCacheKey')) {
            return;
        }

        self::$cache->put($model->getTranslationCacheKey(), $entity);
    }
}

<?php
namespace Tectonic\LaravelLocalisation\Translator\Transformers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tectonic\LaravelLocalisation\Database\Translation;
use Tectonic\Localisation\Contracts\Transformer;
use Tectonic\Localisation\Translator\Transformers\Transformer as BaseTransformer;

class CollectionTransformer extends BaseTransformer implements Transformer
{
    /**
     * This transformer is only appropriate for collection objects.
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
     * @param  Collection  $collection
     * @param  null  $language
     * @return mixed
     */
    public function transform($collection, $language = null)
    {
        return $this->translate($collection, false);
    }

    /**
     * Same as transform but should only translate objects one-level deep. With collections, we always
     * have to pass off to the model transformer anyway, so just simply re-call the transform method.
     *
     * @param  object  $collection
     * @param  null  $language
     * @param  null  $fields
     * @return mixed
     */
    public function shallow($collection, $language = null, $fields = null)
    {
        return $this->translate($collection, true);
    }

    /**
     * Translates the collection.
     *
     * @param $collection
     * @param boolean $shallow
     * @return Collection
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
     *
     * @return Collection
     */
    public function applyTranslations(Collection $collection, Collection $translations, bool $shallow)
    {
        $translations = $translations->groupBy(fn ($translation) => $this->groupByKey($translation));

        foreach ($collection as $model) {
            if (($modelTranslations = $this->modelTranslations($translations, $model, $shallow))->isNotEmpty()) {
                app(ModelTransformer::class)->applyTranslations($model, $modelTranslations, $shallow);
            }
        }

        return $collection;
    }

    protected function modelTranslations(Collection $translations, Model $model, bool $shallow = false)
    {
        $relationTranslations = ($translations->get($this->groupByKey($model), collect()))->toBase();

        if (!$shallow && $relations = array_filter($model->getRelations())) {
            $relationTranslations = $relationTranslations->merge(
                $this->parseRelationsTranslations($relations, $translations)
            );
        }

        return new \Illuminate\Database\Eloquent\Collection(
            $relationTranslations
                ->filter()
                ->flatten()
        );
    }

    protected function parseRelationsTranslations(array $relations, Collection $translations): Collection
    {
        $relationTranslations = collect();
        foreach ($relations as $relation) {
            if ($relation instanceof Collection) {
                foreach ($relation as $item) {
                    $relationTranslations = $relationTranslations->merge(
                        $this->modelTranslations($translations, $item)
                            ->groupBy(fn ($item) => $this->groupByKey($item))
                    );
                }
            } elseif ($relation) {
                $relationTranslations->push($translations->get($this->groupByKey($relation)));
                if (array_filter($relation->getRelations())) {
                    $relationTranslations = $relationTranslations->merge(
                        $this->parseRelationsTranslations($relation->getRelations(), $translations)
                    );
                }
            }
        }

        return $relationTranslations;
    }

    protected function groupByKey($item)
    {
        if ($item instanceof Translation || $item instanceof \stdClass) {
            return $item->foreign_id.$item->resource;
        }

        return $item->id.class_basename($item);
    }
}

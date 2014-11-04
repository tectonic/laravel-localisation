<?php
namespace Tectonic\LaravelLocalisation\Database;

use Tectonic\Localisation\Contracts\TranslationRepositoryInterface;
use Tectonic\Localisation\Translator\ResourceCriteria;

class TranslationRepository implements TranslationRepositoryInterface
{
    /**
     * @var Translation
     */
    private $model;

    /**
     * @param Translation $model
     */
    public function __construct(Translation $model)
    {
        $this->model = $model;
    }

    /**
     * When searching for translations to be applied to an entity, or a collection of entities,
     * we want to do so in the most manner possible. In this way, any repository you have
     * that searches for translations, should do so based on the ResourceCriteria object passed.
     *
     * @param ResourceCriteria $criteria
     * @return mixed
     */
    public function getByResourceCriteria(ResourceCriteria $criteria)
    {
        $resources = $criteria->getResources();
        $query = $this->model->getQuery();

        foreach ($resources as $resource) {
            $query->orWhere(function($query) use ($criteria, $resource) {
                $query->whereResource($resource);
                $query->whereIn('foreign_id', $criteria->getIds($resource));
            });
        }

        return $query->get();
    }

    /**
     * Create a new translation record and return the model.
     *
     * @param $language
     * @param $resource
     * @param $foreignId
     * @param $field
     * @param $value
     * @return Translation
     */
    public function create($language, $resource, $foreignId, $field, $value)
    {
        $model = $this->model->newInstance();

        $model->language   = $language;
        $model->foreign_id = $foreignId;
        $model->resource   = $resource;
        $model->field      = $field;
        $model->value      = $value;

        $model->save();

        return $model;
    }
}

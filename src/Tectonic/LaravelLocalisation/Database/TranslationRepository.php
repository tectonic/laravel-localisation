<?php
namespace Tectonic\LaravelLocalisation\Database;

use Tectonic\Shift\Modules\Localisation\Translator\Contracts\ResourceCriteria;
use Tectonic\Shift\Modules\Localisation\Translator\Contracts\TranslationRepositoryInterface;

class TranslationRepository implements TranslationRepositoryInterface
{
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
        $query = $this->getQuery();

        foreach ($resources as $resource) {
            $query ->orWhere(function($query) use ($criteria, $resource) {
                $query->whereResource($resource);
                $query->whereIn('foreignId', $criteria->getIds($resource));
            });
        }

        return $query->get();
    }
}
 
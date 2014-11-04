<?php
namespace Tectonic\LaravelLocalisation\Translator\Translated;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
    /**
     * Add a new translated entity to the item list.
     *
     * @param Entity $entity
     */
    public function add(Entity $entity)
    {
        $this->push($entity);
    }
}

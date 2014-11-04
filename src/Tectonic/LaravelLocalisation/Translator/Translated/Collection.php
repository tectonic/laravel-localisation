<?php
namespace Tectonic\LaravelLocalisation\Translator\Translated;

use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{
	public function add(Entity $entity)
    {
        $this->push($entity);
    }
}

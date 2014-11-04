<?php
namespace Tests\Translator\Translated;

use Tests\TestCase;
use Tectonic\LaravelLocalisation\Translator\Translated\Collection;
use Tectonic\LaravelLocalisation\Translator\Translated\Entity;

class CollectionTest extends TestCase
{
	public function testEntityAdding()
    {
        $entity = new \Tectonic\LaravelLocalisation\Translator\Translated\Entity;

        $collection = new Collection();
        $collection->add($entity);

        $this->assertEquals($entity, $collection->first());
    }
}
 
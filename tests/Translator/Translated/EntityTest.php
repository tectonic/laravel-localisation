<?php
namespace Tests\Translator\Translated;

use Tectonic\LaravelLocalisation\Translator\Translated\Entity;
use Tests\TestCase;

class EntityTest extends TestCase
{
	public function init()
    {
        $this->entity = new Entity(['name' => 'Me']);
    }

    public function testAttributeRetrieval()
    {
        $this->assertEquals('Me', $this->entity->name);
    }
}

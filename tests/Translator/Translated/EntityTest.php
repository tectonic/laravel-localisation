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

    public function testAttributeSetting()
    {
        $this->entity->address = 'Some place';

        $this->assertEquals('Some place', $this->entity->address);
    }

    public function testFieldTranslationApplication()
    {
        $this->entity->applyTranslation('en_GB', 'eyeColour', 'Hazel');

        $this->assertEquals(['en_GB' => 'Hazel'], $this->entity->eyeColour);
    }
}

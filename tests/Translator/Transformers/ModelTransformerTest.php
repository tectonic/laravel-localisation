<?php
namespace Tests\Translator\Transformers;

use Tectonic\LaravelLocalisation\Translator\Transformers\ModelTransformer;
use Tests\TestCase;

class ModelTransformerTest extends TestCase
{
	public function init()
    {
        $this->modelTransformer = new ModelTransformer;
    }
}

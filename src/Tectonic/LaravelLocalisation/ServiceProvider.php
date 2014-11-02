<?php
namespace Tectonic\LaravelLocalisation;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Tectonic\LaravelLocalisation\Translator\Transformers\CollectionTransformer;
use Tectonic\Localisation\Translator\Engine;
use Tectonic\Shift\Modules\Localisation\Translator\Transformers\ModelTransformer;

class ServiceProvider extends LaravelServiceProvider
{
	public function register()
    {
        $this->registerTranslator();
    }

    private function registerTranslator()
    {
        $this->app->singleton('tectonic.localisation.translator', function($app) {
            $translatorEngine = new Engine;
            $translatorEngine->registerTransformer(
                new ModelTransformer,
                new CollectionTransformer
            );

            return $translatorEngine;
        });
    }
}

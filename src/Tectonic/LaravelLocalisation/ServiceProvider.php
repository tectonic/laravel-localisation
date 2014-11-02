<?php
namespace Tectonic\LaravelLocalisation;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Tectonic\LaravelLocalisation\Translator\Transformers\CollectionTransformer;
use Tectonic\Localisation\Translator\Engine;
use Tectonic\Localisation\Contracts\TranslationRepositoryInterface;
use Tectonic\Shift\Modules\Localisation\Translator\Transformers\ModelTransformer;

class ServiceProvider extends LaravelServiceProvider
{
	public function register()
    {
        $this->registerTranslationRepository();
        $this->registerModelTransformer();
        $this->registerCollectionTransformer();
        $this->registerTranslator();
    }

    private function registerTranslationRepository()
    {
        $this->app->singleton(TranslationRepositoryInterface::class, TranslationRepository::class);
    }

    private function registerModelTransformer()
    {
        $this->app->bind(ModelTransformer::class, function() {
            $modelTransformer = new ModelTransformer;
            $modelTransformer->setTranslationRepository(App::make(TranslationRepositoryInterface::class));

            return $modelTransformer;
        });
    }

    private function registerCollectionTransformer()
    {
        $this->app->bind(CollectionTransformer::class, function() {
            $collectionTransformer = new CollectionTransformer;
            $collectionTransformer->setTranslationRepository(TranslationRepositoryInterface::class);

            return $collectionTransformer;
        });
    }

    private function registerTranslator()
    {
        $this->app->singleton('tectonic.localisation.translator', function() {
            $translatorEngine = new Engine;

            $translatorEngine->registerTransformer(
                new ModelTransformer,
                new CollectionTransformer
            );

            return $translatorEngine;
        });
    }
}

<?php
namespace Tectonic\LaravelLocalisation;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Tectonic\LaravelLocalisation\Translator\Transformers\CollectionTransformer;
use Tectonic\Localisation\Translator\Engine;
use Tectonic\Localisation\Contracts\TranslationRepositoryInterface;
use Tectonic\Localisation\Translator\Transformers\ModelTransformer;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the service provider and all associated classes and bindings.
     */
    public function register()
    {
        $this->registerTranslationRepository();
        $this->registerModelTransformer();
        $this->registerCollectionTransformer();
        $this->registerTranslator();
    }

    /**
     * Set up the translation repository interface, binding it to the LaravelLocalisation implementation.
     */
    private function registerTranslationRepository()
    {
        $this->app->singleton(TranslationRepositoryInterface::class, TranslationRepository::class);
    }

    /**
     * Register the model transformer. It depends on the repository interface, which we need to set
     * whenever the transformer is called via App::make.
     */
    private function registerModelTransformer()
    {
        $this->app->bind(ModelTransformer::class, function() {
            $modelTransformer = new ModelTransformer;
            $modelTransformer->setTranslationRepository(App::make(TranslationRepositoryInterface::class));

            return $modelTransformer;
        });
    }

    /**
     * Register the collection transformer. It depends on the repository interface, which we need to set
     * whenever the transformer is called via App::make.
     */
    private function registerCollectionTransformer()
    {
        $this->app->bind(CollectionTransformer::class, function() {
            $collectionTransformer = new CollectionTransformer;
            $collectionTransformer->setTranslationRepository($this->app->make(TranslationRepositoryInterface::class));

            return $collectionTransformer;
        });
    }

    /**
     * Register the translator that is used by the facade.
     */
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

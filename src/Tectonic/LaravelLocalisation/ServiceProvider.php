<?php
namespace Tectonic\LaravelLocalisation;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Tectonic\LaravelLocalisation\Database\TranslationRepository;
use Tectonic\LaravelLocalisation\Translator\Transformers\CollectionTransformer;
use Tectonic\LaravelLocalisation\Translator\Transformers\ModelTransformer;
use Tectonic\Localisation\Translator\Engine;
use Tectonic\Localisation\Contracts\TranslationRepositoryInterface;

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
        $this->app->bind(ModelTransformer::class, function($app) {
            $modelTransformer = new ModelTransformer;
            $modelTransformer->setTranslationRepository($app->make(TranslationRepositoryInterface::class));

            return $modelTransformer;
        });
    }

    /**
     * Register the collection transformer. It depends on the repository interface, which we need to set
     * whenever the transformer is called via App::make.
     */
    private function registerCollectionTransformer()
    {
        $this->app->bind(CollectionTransformer::class, function($app) {
            $collectionTransformer = new CollectionTransformer;
            $collectionTransformer->setTranslationRepository($app->make(TranslationRepositoryInterface::class));

            return $collectionTransformer;
        });
    }

    /**
     * Register the translator that is used by the facade.
     */
    private function registerTranslator()
    {
        $this->app->singleton('tectonic.localisation.translator', function($app) {
            $translatorEngine = new Engine;

            $translatorEngine->registerTransformer(
                $app->make(ModelTransformer::class),
                $app->make(CollectionTransformer::class)
            );

            return $translatorEngine;
        });
    }
}

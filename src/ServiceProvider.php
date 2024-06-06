<?php
namespace Tectonic\LaravelLocalisation;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Tectonic\LaravelLocalisation\Database\EloquentTranslationRepository;
use Tectonic\LaravelLocalisation\Translator\Engine;
use Tectonic\LaravelLocalisation\Translator\Transformers\CollectionTransformer;
use Tectonic\LaravelLocalisation\Translator\Transformers\ModelTransformer;
use Tectonic\LaravelLocalisation\Translator\Transformers\PaginationTransformer;
use Tectonic\Localisation\Contracts\TranslationRepository;

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

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/localisation.php' => config_path('localisation.php')
        ]);
    }

    /**
     * Set up the translation repository interface, binding it to the LaravelLocalisation implementation.
     */
    private function registerTranslationRepository()
    {
        $this->app->scoped(TranslationRepository::class, function($app) {
            $model = $app['config']->get('localisation.model');

            return new EloquentTranslationRepository(new $model);
        });
    }

    /**
     * Register the model transformer. It depends on the repository interface, which we need to set
     * whenever the transformer is called via App::make.
     */
    private function registerModelTransformer()
    {
        $this->app->scoped(ModelTransformer::class, function($app) {
            $modelTransformer = new ModelTransformer;
            $modelTransformer->setTranslationRepository($app->make(TranslationRepository::class));

            return $modelTransformer;
        });
    }

    /**
     * Register the collection transformer. It depends on the repository interface, which we need to set
     * whenever the transformer is called via App::make.
     */
    private function registerCollectionTransformer()
    {
        $this->app->scoped(CollectionTransformer::class, function($app) {
            $collectionTransformer = new CollectionTransformer;
            $collectionTransformer->setTranslationRepository($app->make(TranslationRepository::class));

            return $collectionTransformer;
        });
    }

    /**
     * Register the translator that is used by the facade.
     */
    private function registerTranslator()
    {
        $this->app->scoped('localisation.translator', function($app) {
            $translatorEngine = new Engine;

            $translatorEngine->registerTransformer(
                $app->make(ModelTransformer::class),
                $app->make(CollectionTransformer::class),
                $app->make(PaginationTransformer::class)
            );

            return $translatorEngine;
        });

        // Register the Engine alias so it that it can simply be injected
        $this->app->alias('localisation.translator', Engine::class);
    }
}

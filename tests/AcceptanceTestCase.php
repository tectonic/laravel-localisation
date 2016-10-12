<?php
namespace Tests;

use Orchestra\Testbench\TestCase;
use Tectonic\LaravelLocalisation\Database\Translation;
use Tectonic\LaravelLocalisation\ServiceProvider;

class AcceptanceTestCase extends TestCase
{
    use Testable;

    public function setUp()
    {
        parent::setUp();

        $migrations = $this->app->make('migration.repository');
        $migrations->createRepository();

        $migrator = $this->app->make('migrator');
        $migrator->run(__DIR__ . '/../migrations');
        $migrator->run(__DIR__.'/Fixtures/Migrations');

        $this->init();
    }

    /**
     * Define environment setup.
     *
     * @param  Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // reset base path to point to our package's src directory
        $app['path.base'] = __DIR__ . '/../';
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', array(
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ));

        $app['config']->set('localisation.model', new Translation);
    }

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}

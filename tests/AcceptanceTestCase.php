<?php
namespace Tests;

use Orchestra\Testbench\TestCase;

class AcceptanceTestCase extends TestCase
{
    use Testable;

    public function setUp()
    {
        parent::setUp();

        $artisan = $this->app->make('artisan');
        $artisan->call('migrate', [
            '--database' => 'test',
            '--path'     => 'src/migrations',
        ]);

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
    }
}

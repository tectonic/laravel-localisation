<?php
namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as IlluminatedTestCase;
use Mockery as m;
use Orchestra\Testbench\Concerns\CreatesApplication;

class TestCase extends IlluminatedTestCase
{
    use RefreshDatabase;
    use Testable;
    use CreatesApplication;

	public function setUp(): void
    {
        parent::setUp();
        m::close();
        $this->init();
    }
}

<?php
namespace Tests;

use Mockery as m;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use Testable;

	public function setUp(): void
    {
        m::close();

        $this->init();
    }
}

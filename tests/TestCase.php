<?php
namespace Tests;

use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
    use Testable;

	public function setUp()
    {
        m::close();

        $this->init();
    }
}

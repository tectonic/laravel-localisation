<?php
namespace Tests;

use Mockery as m;

class TestCase extends \PHPUnit_Framework_TestCase
{
	public function setUp()
    {
        m::close();

        $this->init();
    }

    protected function init()
    {
        // Extend/overload and do what you need to do. Saves having to call parent::setUp() all the time!
    }
}

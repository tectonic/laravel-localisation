<?php
namespace tests\Database;

use Mockery as m;
use Tectonic\LaravelLocalisation\Database\TranslationService;
use Tectonic\Localisation\Contracts\TranslationRepository;
use Tests\Stubs\Model;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    public function testFind()
    {
        $repository = m::spy(TranslationRepository::class);
        $service = new TranslationService($repository);

        $service->find(1);

        $repository->shouldHaveReceived('getById')->with(1)->once();
    }

    public function testFindAll()
    {
        $repository = m::spy(TranslationRepository::class);
        $service = new TranslationService($repository);

        $service->findAll([]);

        $repository->shouldHaveReceived('getByCriteria')->with([])->once();
    }

    public function testTranslationCreation()
    {
        $repository = m::mock(TranslationRepository::class);
        $service = new TranslationService($repository);
        $translation = new \stdClass;
        $model = new Model;

        $repository->shouldReceive('getNew')->once()->andReturn($translation);
        $repository->shouldReceive('save')->once()->with($translation);

        $this->assertEquals($translation, $service->create($model, 'en', 'name', 'colours'));
    }

    /**
     * @expectedException Tectonic\LaravelLocalisation\Database\TranslationNotFound
     */
    public function testTranslationUpdateWithNullTranslation()
    {
        $repository = m::mock(TranslationRepository::class);
        $repository->shouldReceive('getByCriteria')->once()->andReturn(null);
        $service = new TranslationService($repository);
        $model = new Model;

        $service->update($model, 'en_GB', 'field', 'value');
    }
}

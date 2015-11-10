<?php
namespace Tests\Database;

use App;
use Tectonic\LaravelLocalisation\Database\EloquentTranslationRepository;
use Tectonic\Localisation\Translator\ResourceCriteria;
use Tests\AcceptanceTestCase;

class TranslationRepositoryTest extends AcceptanceTestCase
{
    private $translationRepository;

    protected function init()
    {
        $this->translationRepository = App::make(EloquentTranslationRepository::class);

        $this->translationRepository->create('en_GB', 'Content', 1, 'title', 'This is how you spell colour.');
        $this->translationRepository->create('en_US', 'Content', 1, 'title', 'This is how you spell color.');
        $this->translationRepository->create('en_US', 'Content', 2, 'description', 'Random description text.');
    }

    public function testResourceCriteriaSearch()
    {
        $resourceCriteria = new ResourceCriteria;
        $resourceCriteria->addResource('Content');
        $resourceCriteria->addId('Content', 1);

        $translations = $this->translationRepository->getByResourceCriteria($resourceCriteria);

        $this->assertCount(2, $translations);
        $this->assertEquals('This is how you spell colour.', $translations[0]->value);
        $this->assertEquals('en_GB', $translations[0]->language);
        $this->assertEquals('This is how you spell color.', $translations[1]->value);
        $this->assertEquals('en_US', $translations[1]->language);
    }

    public function testGetByParams()
    {
        $translations = $this->translationRepository->getByCriteria(['foreign_id' => 1]);

        $this->assertCount(2, $translations);
        $this->assertEquals('This is how you spell colour.', $translations[0]->value);
        $this->assertEquals('This is how you spell color.', $translations[1]->value);
    }

    public function testGetOneByParams()
    {
        $translation = $this->translationRepository->getOneByCriteria(['foreign_id' => 1]);

        $this->assertEquals('This is how you spell colour.', $translation->value);
    }
}

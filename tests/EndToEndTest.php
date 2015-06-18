<?php
namespace Tests;

use Tectonic\LaravelLocalisation\Database\Translation;
use Tectonic\LaravelLocalisation\Facades\Translator;
use Tests\Fixtures\Models\Category;
use Tests\Fixtures\Models\Content;

class EndToEndTest extends AcceptanceTestCase
{
    private $category1;
    private $category2;
    private $content1;
    private $content2;
    private $content3;
    private $content4;
    private $content5;

    public function init()
    {
        // Sets up all the required data for the tests below. This is a
        // full end-to-end test of data, relationships translations and more.
        $this->createCategories();

        // Setup the content
        $this->createContent();

        // Now let's setup translations for various pieces of categories and content
        $this->createTranslations();
    }

    /**
     * Tests the retrieval and assignment of translations for categories.
     */
    public function testTranslationRetrievalForCategoriesOnly()
    {
        $category = Category::find($this->category1->id);
        $translated = Translator::translate($category);

        $this->assertEquals('Tucker', $translated->title['en_GB']);
        $this->assertEquals('Food', $translated->title['en_US']);
        $this->assertArrayHasKey('en_US', $translated->getTranslations());
        $this->assertArrayHasKey('en_GB', $translated->getTranslations());
    }

    /**
     * A little more complex, looks at the translated content for content records that have a category.
     */
    public function testTranslationsForAContentRecordAssignedToACategory()
    {
        $content = Content::with('category')->find($this->content1->id);
        $translated = Translator::translate($content);

        $this->assertEquals('This is what we shall do', $translated->title['en_GB']);
        $this->assertEquals('Tucker', $translated->category->title['en_GB']);
    }
    
    /**
     * Tests translations on relationships that have collections.
     */
    public function testTranslationsForCollectionRelationships()
    {
        $category = Category::with('content')->find($this->category1->id);
        $translated = Translator::translate($category);

        $this->assertCount(2, $translated->content);
        $this->assertEquals('This is what we shall do', $translated->content[0]->title['en_GB']);
    }

    /**
     * Tests translations for nested relationship collections.
     */
    public function testTranslationsForCollectionsOfCollections()
    {
        $categories = Category::with('content')->get();
        $translated = Translator::translate($categories);

        $this->assertCount(2, $categories);
        $this->assertEquals('Football', $translated->last()->title['en_GB']);
        $this->assertEquals('This is what we shall do', $translated[0]->content[0]->title['en_GB']);
    }

    /**
     * Create the categories necessary for the tests.
     */
    private function createCategories()
    {
        $this->category1 = Category::create([]);
        $this->category2 = Category::create([]);
    }

    /**
     * Create the content data required for the tests.
     */
    private function createContent()
    {
        $this->content1 = $this->category1->content()->save(new Content);
        $this->content2 = $this->category1->content()->save(new Content);
        $this->content3 = $this->category2->content()->save(new Content);
        $this->content4 = $this->category2->content()->save(new Content);
        $this->content5 = $this->category2->content()->save(new Content);
    }

    /**
     * Create the translations for categories and content.
     */
    private function createTranslations()
    {
        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Category',
            'foreign_id' => $this->category1->id,
            'field' => 'title',
            'value' => 'Tucker'
        ]);

        Translation::create([
            'language' => 'en_US',
            'resource' => 'Category',
            'foreign_id' => $this->category1->id,
            'field' => 'title',
            'value' => 'Food'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Category',
            'foreign_id' => $this->category2->id,
            'field' => 'title',
            'value' => 'Football'
        ]);

        Translation::create([
            'language' => 'en_US',
            'resource' => 'Category',
            'foreign_id' => $this->category2->id,
            'field' => 'title',
            'value' => 'Soccer'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Content',
            'foreign_id' => $this->content1->id,
            'field' => 'title',
            'value' => 'This is what we shall do'
        ]);
    }
}

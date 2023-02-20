<?php
namespace Tests;

use Illuminate\Support\Facades\App;
use Tectonic\LaravelLocalisation\Database\Translation;
use Tectonic\LaravelLocalisation\Facades\Translator;
use Tests\Fixtures\Models\Author;
use Tests\Fixtures\Models\Category;
use Tests\Fixtures\Models\Content;
use Tests\Fixtures\Models\Link;
use Tests\Fixtures\Models\Post;
use Tests\Fixtures\Models\Reviewer;

class EndToEndTest extends AcceptanceTestCase
{
    private $category1;
    private $category2;
    private $content1;
    private $content2;
    private $content3;
    private $content4;
    private $content5;
    private $author1;
    private $author2;
    private $post1;
    private $post2;
    private $post3;
    private $post4;

    public function init()
    {
        // Sets up all the required data for the tests below. This is a
        // full end-to-end test of data, relationships translations and more.
        $this->createCategories();

        // Setup authors
        $this->createAuthors();

        // Setup the content
        $this->createContent();

        // Setup the links
        $this->createLinks();

        // Setup the posts
        $this->createPosts();

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

        $this->assertEquals('Tucker', $translated->trans('en_GB', 'title'));
        $this->assertEquals('Food', $translated->trans('en_US', 'title'));
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

        $this->assertEquals('This is what we shall do', $translated->trans('en_GB', 'title'));
        $this->assertEquals('Tucker', $translated->category->trans('en_GB', 'title'));
    }

    /**
     * Tests translations on relationships that have collections.
     */
    public function testTranslationsForCollectionRelationships()
    {
        $category = Category::with('content')->find($this->category1->id);
        $translated = Translator::translate($category);

        $this->assertCount(2, $translated->content);
        $this->assertEquals('This is what we shall do', $translated->content[0]->trans('en_GB', 'title'));
    }

    /**
     * Tests translations on deep relationships from collection.
     */
    public function testTranslationsForCollectionDeepRelationships()
    {
        $category = Category::with(['content', 'content.links', 'content.author', 'content.author.posts'])->get();

        Post::all()->each(function ($post) {
            $post->delete();
        });
        $translated = Translator::translate($category->load())->first();

        $this->assertCount(2, $translated->content);
        $this->assertCount(2, $translated->content[0]->links);
        $this->assertEquals('This is what we shall do', $translated->content[0]->trans('en_GB', 'title'));
        $this->assertEquals('This is a link', $translated->content[0]->links[0]->trans('en_GB', 'title'));
        $this->assertEquals('Author 1 summary', $translated->content[0]->author->trans('en_GB', 'summary'));
        $this->assertEquals('This is a title 1', $translated->content[0]->author->posts[0]->trans('en_GB', 'title'));
    }

    /**
     * Tests translations on deep relationships from model that have deep relationships.
     */
    public function testTranslationsForModelDeepRelationships()
    {
        $category = Category::with(['content', 'content.links', 'content.author', 'content.author.posts'])->find($this->category2->id);

        $translated = Translator::translate($category);

        $this->assertCount(3, $translated->content);
        $this->assertCount(2, $translated->content[0]->links);
        $this->assertEquals('This is what we shall do', $translated->content[0]->trans('en_GB', 'title'));
        $this->assertEquals('This is a link', $translated->content[0]->links[0]->trans('en_GB', 'title'));
        $this->assertEquals('Author 2 summary', $translated->content[0]->author->trans('en_GB', 'summary'));
        $this->assertEquals('This is a title 3', $translated->content[0]->author->posts[0]->trans('en_GB', 'title'));
    }

    public function testTranslationsForModelDeepRelationshipsWithMissingRelation()
    {
        $reviewer = new Reviewer;

        Link::find(2)->reviewer()->save($reviewer);

        $coll = Author::with([
                                 'content',
//             'content.category',
                                 'content.links',
                                 'content.links.reviewer',
                                 'content.links.content'

                             ])->paginate(1);

        dd($coll);

        Translator::translate($coll);
//        $collection = Post::with(['category', 'category.content', 'category.content.links', 'category.content.author'])->paginate(1);
//
//        dd($collection);
//        $translated = Translator::translate($collection);

//        dd($translated->getRelations());

//        $this->assertCount(3, $translated->content);
//        $this->assertCount(2, $translated->content[0]->links);
//        $this->assertEquals('This is what we shall do', $translated->content[0]->trans('en_GB', 'title'));
//        $this->assertEquals('This is a link', $translated->content[0]->links[0]->trans('en_GB', 'title'));
//        $this->assertEquals('Author 2 summary', $translated->content[0]->author->trans('en_GB', 'summary'));
//        $this->assertEquals('This is a title 3', $translated->content[0]->author->posts[0]->trans('en_GB', 'title'));
    }

    /**
     * Tests translations for nested relationship collections.
     */
    public function testTranslationsForCollectionsOfCollections()
    {
        $categories = Category::with('content')->get();
        $translated = Translator::translate($categories);

        $this->assertCount(2, $categories);
        $this->assertEquals('Football', $translated->last()->trans('en_GB', 'title'));
        $this->assertEquals('This is what we shall do', $translated[0]->content[0]->trans('en_GB', 'title'));
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
     * Create the authors required for the tests.
     */
    private function createAuthors()
    {
        $this->author1 = Author::create([]);
        $this->author2 = Author::create([]);
    }

    /**
     * Create the content data required for the tests.
     */
    private function createContent()
    {
        $this->author1->content()->save($this->content1 = $this->category1->content()->save(new Content));
        $this->author2->content()->save($this->content2 = $this->category1->content()->save(new Content));
        $this->author2->content()->save($this->content3 = $this->category2->content()->save(new Content));
        $this->author2->content()->save($this->content4 = $this->category2->content()->save(new Content));
        $this->author2->content()->save($this->content5 = $this->category2->content()->save(new Content));
    }

    /**
     * Create the link data required for the tests.
     */
    private function createLinks()
    {
        $this->content1->links()->save(new Link);
        $this->content1->links()->save(new Link);
        $this->content2->links()->save(new Link);
        $this->content2->links()->save(new Link);
        $this->content3->links()->save(new Link);
        $this->content3->links()->save(new Link);
        $this->content4->links()->save(new Link);
        $this->content4->links()->save(new Link);
        $this->content5->links()->save(new Link);
        $this->content5->links()->save(new Link);
    }

    /**
     * Create the posts required for the tests.
     */
    private function createPosts()
    {
        $this->author1->posts()->save($this->post1 = new Post);
        $this->author1->posts()->save($this->post2 = new Post);
        $this->author2->posts()->save($this->post3 = new Post);
        $this->author2->posts()->save($this->post4 = new Post);
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

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Content',
            'foreign_id' => $this->content2->id,
            'field' => 'title',
            'value' => 'This is what we shall do'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Content',
            'foreign_id' => $this->content3->id,
            'field' => 'title',
            'value' => 'This is what we shall do'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Content',
            'foreign_id' => $this->content4->id,
            'field' => 'title',
            'value' => 'This is what we shall do'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Author',
            'foreign_id' => $this->author1->id,
            'field' => 'summary',
            'value' => 'Author 1 summary'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Author',
            'foreign_id' => $this->author2->id,
            'field' => 'summary',
            'value' => 'Author 2 summary'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Link',
            'foreign_id' => $this->content1->links[0]->id,
            'field' => 'title',
            'value' => 'This is a link'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Link',
            'foreign_id' => $this->content2->links[1]->id,
            'field' => 'title',
            'value' => 'This is a link'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Link',
            'foreign_id' => $this->content3->links[0]->id,
            'field' => 'title',
            'value' => 'This is a link'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Link',
            'foreign_id' => $this->content4->links[1]->id,
            'field' => 'title',
            'value' => 'This is a link'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Post',
            'foreign_id' => $this->post1->id,
            'field' => 'title',
            'value' => 'This is a title 1'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Post',
            'foreign_id' => $this->post2->id,
            'field' => 'title',
            'value' => 'This is a title 2'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Post',
            'foreign_id' => $this->post3->id,
            'field' => 'title',
            'value' => 'This is a title 3'
        ]);

        Translation::create([
            'language' => 'en_GB',
            'resource' => 'Post',
            'foreign_id' => $this->post4->id,
            'field' => 'title',
            'value' => 'This is a title 4'
        ]);

    }
}

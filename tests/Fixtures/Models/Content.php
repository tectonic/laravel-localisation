<?php
namespace Tests\Fixtures\Models;

class Content extends \Eloquent
{
    public $table = 'content';

	public function category()
    {
        return $this->belongsTo(Category::class);
    }
}

<?php
namespace Tests\Fixtures\Models;

class Category extends \Eloquent
{
	public function content()
    {
        return $this->hasMany(Content::class);
    }
}

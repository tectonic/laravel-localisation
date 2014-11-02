<?php
namespace Tectonic\LaravelLocalisation\Database;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    /**
     * The fillable elements of the record.
     *
     * @var array
     */
    public $fillable = ['foreignId', 'language', 'resource', 'field', 'value'];
}

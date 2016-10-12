<?php
namespace Tectonic\LaravelLocalisation\Database;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    /**
     * We don't really have any need for timestamp columns here.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The fillable elements of the record.
     *
     * @var array
     */
    public $fillable = ['language', 'foreign_id', 'resource', 'field', 'value'];
}

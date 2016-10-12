<?php
namespace Tectonic\LaravelLocalisation\Translator\Transformers;

use App;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Tectonic\Localisation\Contracts\Transformer;

class PaginationTransformer implements Transformer
{
    /**
     * Implementations should take an object as a parameter, and then respond with a boolean
     * true or false depending on whether or not they are able to transform that object.
     *
     * @param $object
     * @return mixed
     */
    public function isAppropriateFor($object)
    {
        return $object instanceof Paginator;
    }

    /**
     * Once a transformer for an object has been found, it then must do whatever work is necessary on that object.
     *
     * @param object $paginator
     * @return mixed
     */
    public function transform($paginator)
    {
        $transformer = App::make(CollectionTransformer::class);

        $originalRecords = new Collection($paginator->items());
        $transformedRecords = $transformer->transform($originalRecords);

        return $this->createNew($paginator, $transformedRecords);
    }

    /**
     * Same as transform but should only translate objects one-level deep.
     *
     * @param object $paginator
     * @return mixed
     */
    public function shallow($paginator)
    {
        return $this->transform($paginator);
    }

    /**
     * Creates a new pagination instance.
     *
     * @param $originalPaginator
     * @param $newRecords
     * @return LengthAwarePaginator
     */
    protected function createNew($originalPaginator, $newRecords)
    {
        $paginator = new LengthAwarePaginator(
            $newRecords,
            $originalPaginator->total(),
            $originalPaginator->perPage(),
            $originalPaginator->currentPage()
        );

        return $paginator;
    }
}

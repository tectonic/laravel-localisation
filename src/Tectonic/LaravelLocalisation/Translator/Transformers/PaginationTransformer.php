<?php
namespace src\Tectonic\LaravelLocalisation\Translator\Transformers;

use Illuminate\Pagination\Paginator;
use Tectonic\LaravelLocalisation\Translator\Transformers\CollectionTransformer;
use Tectonic\Localisation\Contracts\TransformerInterface;

class PaginationTransformer implements TransformerInterface
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
     * @param object $object
     * @return mixed
     */
    public function transform($object)
    {
        $transformer = new CollectionTransformer;
        $originalRecords = $object->getItems();
        $transformedRecords = $transformer->transform($originalRecords);
        $object->setItems($transformedRecords);

        return $object;
    }
}

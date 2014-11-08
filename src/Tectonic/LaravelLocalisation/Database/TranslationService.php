<?php
namespace src\Tectonic\LaravelLocalisation\Database;

use Tectonic\Localisation\Contracts\TranslationRepositoryInterface;
use Tectonic\Localisation\Translator\Translatable;

class TranslationService
{
    /**
     * @var TranslationRepositoryInterface
     */
    private $translationRepository;

    /**
     * @param TranslationRepositoryInterface $translationRepository
     */
    public function __construct(TranslationRepositoryInterface $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }

    /**
     * Finds a translation by translation id.
     *
     * @param integer $id
     * @return Translation
     */
    public function find($id)
    {
        return $this->translationRepository->getById($id);
    }

    /**
     * Searches for any translations based on the criteria paramters provided.
     *
     * @param array $params
     */
    public function findAll(array $params)
    {
        return $this->translationRepository->getByCriteria($params);
    }

    /**
     * Creates a new translation for a given translatable model, saving the language, field and value.
     *
     * @param Translatable $model
     * @param $language
     * @param $field
     * @param $value
     * @return mixed
     */
    public function create(Translatable $model, $language, $field, $value)
    {
        $translation = $this->translationRepository->getNew();

        $translation->language = $language;
        $translation->field = $field;
        $translation->value = $value;
        $translation->resource = $model->getResourceName();
        $translation->foreignId = $model->getId();

        $this->translationRepository->save($translation);

        return $translation;
    }

    public function update(Translatable $model, $language, $field, $value)
    {
        $translation = $this->translationRepository->getByCriteria([
            'language' => $language,
            'resource' => $model->getResourceName(),
            'foreign_id' => $model->getId(),
            'field' => $field
        ]);

        if ($translation) {
            $translation = $translation[0];
        }

        $translation->value = $value;

        $this->translationRepository->save($translation);

        return $translation;
    }

    /**
     * Deletes a translation from the system based on the id.
     *
     * @param integer $id
     */
    public function delete($translation)
    {
        $this->translationRepository->delete($translation);
    }

    public function deleteAll(...$translations)
    {
        foreach ($translations as $translation) {
            $this->translationRepository->delete($translation);
        }
    }
}

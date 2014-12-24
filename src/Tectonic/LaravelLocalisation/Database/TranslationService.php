<?php
namespace src\Tectonic\LaravelLocalisation\Database;

use Tectonic\LaravelLocalisation\Database\TranslationNotFound;
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

    /**
     * Update an existing translation record based on the data provided.
     *
     * @param Translatable $model
     * @param $language
     * @param $field
     * @param $value
     * @return null|Translation
     * @throws TranslationNotFound
     */
    public function update(Translatable $model, $language, $field, $value)
    {
        $translation = $this->findForUpdate($model, $language, $field);

        if (is_null($translation)) {
            throw new TranslationNotFound;
        }

        $translation->value = $value;

        $this->translationRepository->save($translation);

        return $translation;
    }

    /**
     * Create a new translation record, or update an existing one.
     *
     * @param Translatable $model
     * @param $language
     * @param $field
     * @param $value
     * @return mixed|null|Translation
     */
    public function createOrUpdate(Translatable $model, $language, $field, $value)
    {
        try {
            return $this->update($model, $language, $field, $value);
        }
        catch (TranslationNotFound $exception) {
            return $this->create($model, $language, $field, $value);
        }
    }

    /**
     * Attempts to find a translation record that will be used for an update at a later period.
     *
     * @param Translatable $model
     * @param $language
     * @param $field
     * @return null|Translation
     */
    public function findForUpdate(Translatable $model, $language, $field)
    {
        $translations = $this->translationRepository->getByCriteria([
            'language' => $language,
            'resource' => $model->getResourceName(),
            'foreign_id' => $model->getId(),
            'field' => $field
        ]);

        if ($translations) {
            return $translations[0];
        }

        return null;
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

    /**
     * Deletes a number of translation objects at once.
     *
     * @param ...$translations
     */
    public function deleteAll(...$translations)
    {
        foreach ($translations as $translation) {
            $this->translationRepository->delete($translation);
        }
    }
}

<?php
namespace Tectonic\LaravelLocalisation\Database;

use Tectonic\Localisation\Contracts\Translatable;
use Tectonic\Localisation\Contracts\TranslationRepository;

class TranslationService
{
    /**
     * @var TranslationRepository
     */
    private $translations;

    /**
     * @param TranslationRepository $translations
     */
    public function __construct(TranslationRepository $translations)
    {
        $this->translations = $translations;
    }

    /**
     * Finds a translation by translation id.
     *
     * @param integer $id
     * @return Translation
     */
    public function find($id)
    {
        return $this->translations->getById($id);
    }

    /**
     * Searches for any translations based on the criteria parameters provided.
     *
     * @param array $params
     * @return mixed
     */
    public function findAll(array $params)
    {
        return $this->translations->getByCriteria($params);
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
        $translation = $this->translations->getNew();

        $translation->language = $language;
        $translation->field = $field;
        $translation->value = $value;
        $translation->resource = $model->getResourceName();
        $translation->foreignId = $model->getId();

        $this->translations->save($translation);

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

        $this->translations->save($translation);

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
     * Synchronises a translatable model, its associated translations - with the database.
     *
     * @param Translatable $model
     * @param array $translations Must follow the following format:
     *
     *   [ 'field' => [ 'languageCode' => 'value' ] ]
     *
     *   eg.
     *
     *   [ 'name' =>
     *     [
     *       'en_GB' => 'Colours',
     *       'en_US' => 'Colors'
     *     ]
     *   ]
     *
     * @return void
     */
    public function sync(Translatable $model, array $translations)
    {
        foreach ($translations as $field => $values) {
            foreach ($values as $languageCode => $value) {
                $this->createOrUpdate($model, $languageCode, $field, $value);
            }
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
        $translations = $this->translations->getByCriteria([
            'language' => $language,
            'resource' => $model->getResourceName(),
            'foreign_id' => $model->getId(),
            'field' => $field
        ]);

        if (count($translations)) {
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
        $this->translations->delete($translation);
    }

    /**
     * Deletes a number of translation objects at once.
     *
     * @param array $translations
     */
    public function deleteAll(array $translations)
    {
        foreach ($translations as $translation) {
            $this->translations->delete($translation);
        }
    }
}

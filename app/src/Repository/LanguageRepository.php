<?php

namespace App\Repository;

use App\Entity\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Language|null find($id, $lockMode = null, $lockVersion = null)
 * @method Language|null findOneBy(array $criteria, array $orderBy = null)
 * @method Language[]    findAll()
 * @method Language[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Array|null    findAllTranslations(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageRepository extends ServiceEntityRepository
{
    /**
     * Constructor for registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }

    /**
     * This method returns all languages with their translations as associated array.
     * @param criteria  criteria to filter languages
     * @param orderBy   rule for languages order
     * @param limit     when provided - returns <limit> number of languages
     * @param offset    when provided - returns languages starting from <offset>
     * @return array multidimensional array of translations per language, sturcture is following:
     *               {"<Language.ISO>":{"<Key.keyCode>":"<Translation.translation>"}}
     */
    public function exportTranslations(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        /**
         * Fetch all translations per language into formattedDataAsc variable.
         * This could be done with one query on Translations table,
         * but was splitted per language for better performance on huge data.
         */
        
        $languages = $this->findBy($criteria, $orderBy, $limit, $offset);
        $formattedDataAsc = array();
        foreach ($languages as $language) {
            foreach ($language->getTranslations() as $translation) {
                $tLang = $translation->getLanguage()->getIso();
                $tKey = $translation->getKey()->getKeyCode();

                if (!isset($formattedDataAsc[$tLang])) {
                    $formattedDataAsc[$tLang] = array();
                }
                if (!isset($formattedDataAsc[$tLang][$tKey])) {
                    $formattedDataAsc[$tLang][$tKey] = $translation->getTranslation();
                }
            }
        }
        unset($languages);

        return $formattedDataAsc;
    }
}

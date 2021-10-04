<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Language;
use App\Entity\Key;
use App\Entity\Translation;
use Symfony\Component\HttpFoundation\Request;

/**
 * Performs export of existing translations per language.
 *
 * Exports all Keys and their Translations in .json or .yaml format as a zip archive depending on format parameter set.
 * @var string format format could be either `json` or `yaml`:
 *  - json export have one file per language (e.g. [language-iso].json) with the following format:
 *    {
 *        <key.name>: <translation.value>,
 *        ...
 *    }
 *  - yaml export have all languages in a single translations.yaml file with the following format:
 *    <language.iso>:
 *        <key.name>: <translation.value>
 *        ...
 *    <language2.iso>:
 *       <key.name>: <translation.value>
 *       ...
 */
class TranslationsExportController extends AbstractController
{
    /**
     * @Route(
     *     name="keys_export_controller",
     *     path="/api/translations/export",
     *     methods={"GET"},
     *     defaults={"_api_item_operation_name"="translations_export"}
     * )
     * @return JsonResponse export response
     */
    public function __invoke(Request $request): JsonResponse
    {
        //TODO put this into message bus and return order ID upon request, as export could
        //take a lot of time on big data volumes.

        /**
         * Fetch format from request GET parameter `format`.
         * It could be only `json` or `yaml`.
         * `json` is used by default.
         */
        $format = 'json';
        if ($request->query->has('format')) {
            $queryFormat = $request->query->get('format');
            if (in_array($queryFormat, array(
                'json',
                'yaml'
            ))) {
                $format = $queryFormat;
            }
        }

        /**
         * Fetch all translations per language into formattedDataAsc variable.
         * This could be done with one query on Translations table,
         * but was splitted per language for better performance on huge data.
         */
        $em =  $this->getDoctrine()->getManager();
        $languages = $em
            ->getRepository(Language::class)
            ->findAll();
        $formattedDataAsc = array();
        foreach ($languages as $language) {
            $translations = $em
                ->getRepository(Translation::class)
                ->findBy(['language' => $language->getIso()]);

            foreach ($translations as $translation) {
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
        unset($translations);

        /**
         * Return document as JSON object which has type, name and content encode with base64.
         * Recipient should decode content and put it into file to have a valid zip archive in recipients system.
         */
        $responseDocument = array(
            'contentType' => 'application/zip',
            'base64Content' => ''
        );
        /**
         * tmpfile() function used as it creates temporary file and destroy it when script ends, no need to clean ourselves.
         */
        $tempArchive = tmpfile();
        $tempArchivePath = stream_get_meta_data($tempArchive)['uri'];
        $zip = new \ZipArchive();
        $zip->open($tempArchivePath);

        /**
         * Logic related to formats is different. Since we store data in one .yaml file, but
         * for JSON we store it in multiple files per each language.
         */
        if ('yaml' === $format) {
            $formattedDataAsc = yaml_emit($formattedDataAsc);
            $zip->addFromString('translations.yaml', $formattedDataAsc);
        } elseif ('json' === $format) {
            foreach ($formattedDataAsc as $lang => $keys) {
                $zip->addFromString($lang.'.json', json_encode($keys));
            }
        }

        /**
         * Fetch created ZIP content, encode it with base64 and put into response
         */
        $zip->close();
        $responseDocument['base64Content'] = base64_encode(file_get_contents($tempArchivePath));
        unset($tempArchive);

        return new JsonResponse($responseDocument);
    }
}

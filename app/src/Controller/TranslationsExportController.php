<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\LanguageRepository;
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
     * Controller for api/translation/export method.
     * This method returns list of translations per each language.
     * @return Response zip file containing either json/yaml (depending on format parameter) files with translations
     */
    public function __invoke(Request $request, LanguageRepository $languageRepository): Response
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

        $formattedTranslations = $languageRepository->exportTranslations();

        /**
         * Return document as JSON object which has type, name and content encode with base64.
         * Recipient should decode content and put it into file to have a valid zip archive in recipients system.
         */
        $responseDocument = array(
            'contentType' => 'application/zip',
            'base64Content' => ''
        );
        /**
         * tmpnam function provides path to system temporary file, which should be cleaned when not needed anymore.
         */
        $tempArchivePath = tempnam('', 'zip_');
        rename($tempArchivePath, $tempArchivePath .= '.zip');
        $zip = new \ZipArchive();
        $zip->open($tempArchivePath);

        /**
         * Logic related to formats is different. Since we store data in one .yaml file, but
         * for JSON we store it in multiple files per each language.
         */
        if ('yaml' === $format) {
            $zip->addFromString('translations.yaml', yaml_emit($formattedTranslations));
        } elseif ('json' === $format) {
            foreach ($formattedTranslations as $lang => $keys) {
                $zip->addFromString($lang.'.json', json_encode($keys));
            }
        }
        /**
         * Fetch created ZIP content, encode it with base64 and put into response
         */
        $zip->close();
        
        /**
        * Create response as zip file stream
        */
        $responseObj = new Response(file_get_contents($tempArchivePath));
        $disposition = $responseObj->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            basename($tempArchivePath)
        );
        $responseObj->headers->set('Content-Disposition', $disposition);
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        if ($mimeTypeGuesser->isGuesserSupported()) {
            $responseObj->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($tempArchivePath));
        } else {
            $responseObj->headers->set('Content-Type', 'text/plain');
        }

        /**
         * Cleaning temporary created zip archive
         */
        unlink($tempArchivePath);

        return $responseObj;
    }
}

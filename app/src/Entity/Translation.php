<?php
/**
 * Entity describing translation object.
 * This object contains translation for specific Key and specific Language.
 * ApiPlatform connected to make operations with entity open for the API.
 */
namespace App\Entity;
 
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Controller\TranslationsExportController;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="translation",
 *              uniqueConstraints={
 *                  @UniqueConstraint(name="uniq_translation_key_language",
 *                      columns={"language_ISO", "key_id"})
 *              })
 * @ApiResource(
 *     collectionOperations={
 *         "get",
 *         "post"={
 *              "security"="is_granted('ROLE_FULL')"
 *         },
 *         "export"={
 *             "method" = "GET",
 *             "path" = "/translations/export",
 *             "controller" = TranslationsExportController::class,
 *             "defaults"={"_api_receive"=false},
 *             "read"=false,
 *             "pagination_enabled"=false,
 *             "filters"={},
 *             "openapi_context" = {
 *                 "summary" = "Export translations",
 *                 "description" = "Based on format specified (json as default) export all translations created in the system into ZIP Archive and returns archive encoded in base64",
 *                 "parameters" = {
 *                     {
 *                         "name" = "format",
 *                         "in" = "path",
 *                         "required" = false,
 *                         "type" = "string",
 *                         "enum" = {"json","yaml"},
 *                         "description" = "Format of export (default - json), could be:<br>json<br>yaml"
 *                      }
 *                  },
 *                  "responses" = {
 *                      "200" = {
 *                        "description" = "ZIP Archive binary file.",
 *                        "content" = {
 *                            "application/zip" = {
 *                                "type" = "object"
 *                            }
 *                        }
 *                      }
 *                  },
 *                  "normalization_context" = {"groups" = "translations_export"}
 *             },
 *             "security"="is_granted('ROLE_FULL')",
 *             "output_formats"={"zip"={"application/zip"}}
 *         }
 *     },
 *     itemOperations={
 *         "get",
 *         "patch"={"security"="is_granted('ROLE_FULL')"},
 *         "delete"={"security"="is_granted('ROLE_FULL')"},
 *     },
 *     attributes={"security"="is_granted('ROLE_USER')"}
 * )
 * @ApiFilter(SearchFilter::class, properties={"language":"exact","key":"exact"})
 */
class Translation
{
    /**
     * Auto generated uuid.
     *
     * @var \Ramsey\Uuid\UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups("translation")
     */
    private string $id;

    /**
     * Related Language entity IRI path.
     *
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="translations")
     * @ORM\JoinColumn(name="language_ISO", referencedColumnName="iso", nullable=false)
     * @Assert\Valid()
     */
    private $language;

    /**
     * Related Key entity IRI path.
     *
     * @ORM\ManyToOne(targetEntity="Key", inversedBy="translations")
     * @ORM\JoinColumn(name="key_id", referencedColumnName="id", nullable=false)
     * @Assert\Valid()
     */
    private $key;


    /**
     * Actual translation of Key in specified Langugae.
     *
     * @ORM\Column(length=512)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 512,
     *      minMessage = "Translation must be at least {{ limit }} characters long",
     *      maxMessage = "Translation cannot be longer than {{ limit }} characters"
     * )
     */
    private string $translation;

    /**
     * Getter for key id
     * @return string id
     */
    public function getId(): uuid
    {
        return Uuid::fromString($this->id);
    }

    /**
     * Getter for translation value
     * @return string translation
     */
    public function getTranslation(): string
    {
        return $this->translation;
    }

    /**
     * Setter for translation
     * @return Translation self object
     */
    public function setTranslation(string $translation): self
    {
        $this->translation = $translation;
        return $this;
    }
    
    /**
     * getter for Language entity related to the Translation
     * @return Language language object
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * setter for Language entity related to the Translation
     * @return Translation self object
     */
    public function setLanguage(Language $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * getter for Key entity related to the Translation
     * @return Key Key object
     */
    public function getKey(): Key
    {
        return $this->key;
    }

    /**
     * setter for Key entity related to the Translation
     * @return Translation self object
     */
    public function setKey(Key $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * When object casts to string - return translation converted to string
     * @return string translation
     */
    public function __toString()
    {
        return $this->translation;
    }
}

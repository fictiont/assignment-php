<?php
/**
 * Entity describing language object
 *
 * Contains model for language table with necessary ORM definitions.
 * ISO-639-2 codes used to identify languages.
 * ApiPlatform connected to make operations with entity open for the API.
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="language", indexes={@ORM\Index(name="language_name", columns={"name"})})
 * @ApiResource(
 *     collectionOperations={
 *         "get",
 *         "post"={
 *              "security"="is_granted('ROLE_FULL')"
 *         }
 *     },
 *     itemOperations={
 *         "get",
 *         "patch"={"security"="is_granted('ROLE_FULL')"},
 *         "delete"={"security"="is_granted('ROLE_FULL')"}
 *     },
 *     attributes={
 *         "security"="is_granted('ROLE_USER')",
 *         "pagination_enabled"=false,
 *         "normalization_context"={"groups"={"get","post"}}
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"iso":"partial","name":"partial"})
 */
class Language
{
    /**
     * Language ISO code using ISO-639-2 standart.
     *
     * @ORM\Id
     * @ORM\Column(length=3, unique=true)
     * @Assert\NotBlank()
     * @Assert\Language(alpha3=true)
     * @Groups({"get","post"})
     */
    private string $iso;

    /**
     * Custom language name in english.
     *
     * @ORM\Column(length=70)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 70,
     *      minMessage = "Language name must be at least {{ limit }} characters long",
     *      maxMessage = "Language name cannot be longer than {{ limit }} characters"
     * )
     * @Groups({"get","post"})
     */
    private string $name;
 
 
    /**
     * When set to true - language is RTL, otherwise - LTR.
     *
     * @ORM\Column(type="boolean")
     * @Groups({"get","post"})
     * Specify if language is Right To Left or opposite.
     */
    private bool $rtl = false;

    /**
     * List of existing translations related to this language.
     *
     * @ORM\OneToMany(targetEntity="Translation", mappedBy="language", cascade={"remove"})
     */
    private $translations;

    /**
     * Getter for language ISO code
     * @return string language code in ISO 639 standard
     */
    public function getIso(): string
    {
        return $this->iso;
    }

    /**
     * Setter for language ISO code
     * @return Language self
     */
    public function setIso(string $code): self
    {
        $this->iso = mb_substr($code, 0, 3, 'UTF-8');

        return $this;
    }

    /**
     * Getter for rtl field
     * @return bool rtl field value
     */
    public function getRtl(): bool
    {
        return $this->rtl;
    }

    /**
     * Setter for rtl field
     * @return bool self
     */
    public function setRtl(bool $rtl): self
    {
        $this->rtl = $rtl;
        
        return $this;
    }

    /**
     * Getter for language name
     * @return string language name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Setter for language name
     * @return Language self
     */
    public function setName($name): self
    {
        $this->name = mb_substr($name, 0, 70, 'UTF-8');

        return $this;
    }
        
    /**
     * Getter for translations
     * @return Doctrine\ORM\PersistentCollection associated translations list
     */
    public function getTranslations(): \Doctrine\ORM\PersistentCollection
    {
        return $this->translations;
    }

    /**
     * When object casts to string - use language name
     * @return string language name
     */
    public function __toString()
    {
        return $this->name;
    }
}

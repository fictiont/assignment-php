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
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
 
/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="language", indexes={@ORM\Index(name="language_name", columns={"name"})})
 * @ApiResource(itemOperations={"get"}, collectionOperations={})
 */
class Language
{
    /**
     * Language ISO code using ISO-639-2 standart.
     *
     * @ORM\Id
     * @ORM\Column(length=3, unique=true)
     * @Assert\NotBlank()
     */
    private string $iso;

    /**
     * Custom language name in english.
     *
     * @ORM\Column(length=70)
     * @Assert\NotBlank()
     */
    private string $name;
 
 
    /**
     * When set to true - language is RTL, otherwise - LTR.
     *
     * @ORM\Column(type="boolean")
     * Specify if language is Right To Left or opposite.
     */
    private bool $rtl = false;

    /**
     * List of existing translations related to this language.
     *
     * @ORM\OneToMany(targetEntity="Translation", mappedBy="language")
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
    public function setIso($code): self
    {
        $this->iso = mb_substr($code, 0, 3, 'UTF-8');

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
     * When object casts to string - use language name
     * @return string language name
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Constructor where we set default values
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
    }
}

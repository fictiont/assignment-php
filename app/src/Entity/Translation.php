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
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="translation",
 *              uniqueConstraints={
 *                  @UniqueConstraint(name="uniq_translation_key_language",
 *                      columns={"language_ISO", "key_id"})
 *              })
 * @ApiResource(
 *     normalizationContext={"groups"={"translation"}},
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('ROLE_FULL')"}
 *     },
 *     itemOperations={
 *         "get",
 *         "patch"={"security"="is_granted('ROLE_FULL')"},
 *         "delete"={"security"="is_granted('ROLE_FULL')"}
 *     },
 *     attributes={"security"="is_granted('ROLE_USER')"}
 * )
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
     * @ORM\JoinColumn(name="language_ISO", referencedColumnName="iso")
     * @Groups("translation")
     */
    private $language;

    /**
     * Related Key entity.
     *
     * @ORM\ManyToOne(targetEntity="Key", inversedBy="translations")
     * @ORM\JoinColumn(name="key_id", referencedColumnName="id")
     * @Groups("translation")
     */
    private $key;


    /**
     * Actual translation of Key in specified Langugae.
     *
     * @ORM\Column(length=512)
     * @Assert\NotBlank()
     * @Groups("translation")
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

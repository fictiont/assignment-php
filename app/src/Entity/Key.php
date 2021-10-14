<?php
/**
 * Entity describing key object
 *
 * Keys are lexems which could have multiple Translations (one per language).
 * Contains model for the "key" table with necessary ORM definitions.
 * ApiPlatform connected to make operations with entity open for the API.
 */
namespace App\Entity;

use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="translation_key")
 * @ApiResource(
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
 * @ApiFilter(SearchFilter::class, properties={"keyCode":"partial"})
 */
class Key
{
    /**
     * Auto generated uuid.
     *
     * @var \Ramsey\Uuid\UuidInterface
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * This is label for translations, for example "main.title".
     *
     * @ORM\Column(length=120, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 120,
     *      minMessage = "Key code must be at least {{ limit }} characters long",
     *      maxMessage = "Key code cannot be longer than {{ limit }} characters"
     * )
     */
    private string $keyCode;

    /**
     * Key description in english language.
     *
     * @ORM\Column(length=512)
     * @Assert\Length(
     *      min = 3,
     *      max = 512,
     *      minMessage = "Key description must be at least {{ limit }} characters long",
     *      maxMessage = "Key description cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotBlank()
     */
    private string $description;

    /**
     * List of translations related to current key.
     *
     * @ORM\OneToMany(targetEntity="Translation", mappedBy="key", cascade={"remove"})
     */
    private $translations;

    /**
     * Getter for key id
     * @return string id
     */
    public function getId(): uuid
    {
        return Uuid::fromString($this->id);
    }

    /**
     * Getter for key keyCode
     * @return string keyCode
     */
    public function getKeyCode(): string
    {
        return $this->keyCode;
    }

    /**
     * Setter for key keyCode
     * @return Entity self
     */
    public function setKeyCode(string $code): self
    {
        $this->keyCode = mb_substr($code, 0, 120, 'UTF-8');
        return $this;
    }

    /**
     * Getter for key description
     * @return string description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Setter for key description
     * @return Entity self
     */
    public function setDescription(string $description): self
    {
        $this->description = mb_substr($description, 0, 512, 'UTF-8');
        return $this;
    }

    /**
     * When object casts to string - return key code
     * @return string key
     */
    public function __toString()
    {
        return $this->keyCode;
    }
}

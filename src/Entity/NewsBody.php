<?php

namespace eap1985\NewsTopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Newsbody
 *
 * @ORM\Table(name="newsbody")
 * @ORM\Entity
 */
class NewsBody
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="bundle", type="string", length=128, nullable=false, options={"comment"="The field instance bundle to which this row belongs, used when deleting a field instance"})
     */
    private $bundle = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false, options={"comment"="A boolean indicating whether this data item has been deleted"})
     */
    private $deleted = '0';

    /**
     * One Cart has One Customer.
     * @ORM\Column(name="entity_id", type="integer", nullable=false, options={"unsigned"=true,"comment"="The entity revision id this data is attached to"})
     */

    private $entityId;

    /**
     * @var int
     *
     * @ORM\Column(name="revision_id", type="integer", nullable=false, options={"unsigned"=true,"comment"="The entity revision id this data is attached to"})
     */
    private $revisionId;

    /**
     * @var string
     *
     * @ORM\Column(name="langcode", type="string", length=32, nullable=false, options={"comment"="The language code for this data item."})
     */
    private $langcode = '';

    /**
     * @var int
     *
     * @ORM\Column(name="delta", type="integer", nullable=false, options={"unsigned"=true,"comment"="The sequence number for this data item, used for multi-value fields"})
     */
    private $delta;

    /**
     * @var string
     *
     * @ORM\Column(name="body_value", type="text", length=0, nullable=false)
     */
    private $bodyValue;

    /**
     * @var string|null
     *
     * @ORM\Column(name="body_summary", type="text", length=0, nullable=true)
     */
    private $bodySummary;

    /**
     * @var string|null
     *
     * @ORM\Column(name="body_format", type="string", length=255, nullable=true)
     */
    private $bodyFormat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBundle(): ?string
    {
        return $this->bundle;
    }

    public function setBundle(string $bundle): self
    {
        $this->bundle = $bundle;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getRevisionId(): ?int
    {
        return $this->revisionId;
    }

    public function setRevisionId(int $revisionId): self
    {
        $this->revisionId = $revisionId;

        return $this;
    }

    public function getLangcode(): ?string
    {
        return $this->langcode;
    }

    public function setLangcode(string $langcode): self
    {
        $this->langcode = $langcode;

        return $this;
    }

    public function getDelta(): ?int
    {
        return $this->delta;
    }

    public function setDelta(int $delta): self
    {
        $this->delta = $delta;

        return $this;
    }

    public function getBodyValue(): ?string
    {
        return $this->bodyValue;
    }

    public function setBodyValue($bodyValue): self
    {
        $this->bodyValue = $bodyValue;

        return $this;
    }

    public function getBodySummary(): ?string
    {
        return $this->bodySummary;
    }

    public function setBodySummary(?string $bodySummary): self
    {
        $this->bodySummary = $bodySummary;

        return $this;
    }

    public function getBodyFormat(): ?string
    {
        return $this->bodyFormat;
    }

    public function setBodyFormat(?string $bodyFormat): self
    {
        $this->bodyFormat = $bodyFormat;

        return $this;
    }

    public function __toString() {
        return $this->bundle;
    }
}

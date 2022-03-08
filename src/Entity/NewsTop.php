<?php

namespace eap1985\NewsTopBundle\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="newstop")
 * @ORM\Entity
 * @Vich\Uploadable()
 * @Assert\Callback("validate")
 */
class NewsTop
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="text", length=65535, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="opisanie", type="text", length=65535, nullable=true)
     */
    private $opisanie;


    /**
     *
     * @ORM\Column(type="datetime",options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="\App\Entity\Category", inversedBy="blogPosts")
     */
    private $category;

    public function __toString() {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }


    /**
     *
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updatedAt;

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


    /**
     * @var string
     * @ORM\Column(name="pdf", type="string", length=200, nullable=true)
     */

    private $pdf;


    public function getPdf(): ?string
    {
        return $this->pdf;
    }


    public function setPdf(?string $pdf): void
    {

        $this->pdf = $pdf;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail", type="string", length=200)
     */

    private $thumbnail;

    /**
     * @Vich\UploadableField(mapping="newstop_pdf", fileNameProperty="pdf")
     * @var File
     * @Assert\File(maxSize="10M")
     */
    private $pdfFile;

    /**
     * @return mixed
     */
    public function getPdfFile()
    {
        return $this->pdfFile;
    }

    /**
     * @param mixed $pdfFile
     * @throws \Exception
     */

    public function setPdfFile($pdfFile): void
    {
        $this->pdfFile = $pdfFile;
        if($this->pdfFile) {
            $this->updatedAt = new DateTime();
        }
    }


    /**
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if(!empty($this->pdfFile)) {
            if (!in_array($this->pdfFile->getMimeType(), array(
                'application/pdf',
            ))) {
                $context
                    ->buildViolation('Wrong file type (pdf)')
                    ->atPath('pdfFile')
                    ->addViolation();
            }
        }

        if(!empty($this->thumbnailFile)) {
            if (!in_array($this->thumbnailFile->getMimeType(), array(
                'image/jpg',
                'image/png',
                'image/gif',
                'image/jpeg',
            ))) {
                $context
                    ->buildViolation('Wrong file type (jpeg,gif,png)')
                    ->atPath('thumbnailFile')
                    ->addViolation();
            }
        }
    }


    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }


    public function setThumbnail(?string $thumbnail): self
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @Vich\UploadableField(mapping="newstop_thumbnails", fileNameProperty="thumbnail")
     */
    private $thumbnailFile;

    /**
     * @return mixed
     */
    public function getThumbnailFile()
    {
        return $this->thumbnailFile;
    }

    /**
     * @param mixed $thumbnailFile
     * @throws \Exception
     */

    public function setThumbnailFile($thumbnailFile): void
    {
        $this->thumbnailFile = $thumbnailFile;
        if($this->thumbnailFile) {
            $this->updatedAt = new DateTime();
        }
    }

    /**
     * @var string
     *
     * @ORM\Column(name="razdel", type="string", length=50, nullable=false)
     */
    private $razdel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="avtor", type="text", length=65535, nullable=true)
     */
    private $avtor;

    /**
     * @var int
     *
     * @ORM\Column(name="count", type="integer", nullable=false)
     */
    private $count;

    /**
     * @var int
     *
     * @ORM\Column(name="comment", type="integer", nullable=false)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime", nullable=false)
     */
    private $time;

    /**
     * @var int
     *
     * @ORM\Column(name="arhiv", type="datetime", nullable=false)
     */
    private $arhiv;

    /**
     * @var string|null
     *
     * @ORM\Column(name="href", type="string", length=50, nullable=true)
     */
    private $href;

    /**
     * @var string
     *
     * @ORM\Column(name="si", type="string", length=255, nullable=false)
     */
    private $si;

    /**
     * @var string
     *
     * @ORM\Column(name="bi", type="string", length=255, nullable=false)
     */
    private $bi;

    /**
     * @var string
     *
     * @ORM\Column(name="metka", type="string", length=255, nullable=false)
     */
    private $metka;

    /**
     * @var int
     *
     * @ORM\Column(name="archived",  type="boolean", nullable=false)
     */
    private $archived;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setCategoryId($category)
    {

        return $this->category = $category;
        return $this;
    }
    public function getCategoryId()
    {
        return $this->category;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOpisanie(): ?string
    {
        return $this->opisanie;
    }

    public function setOpisanie(?string $opisanie): self
    {
        $this->opisanie = $opisanie;

        return $this;
    }

    public function getRazdel(): ?string
    {
        return $this->razdel;
    }

    public function setRazdel(string $razdel): self
    {
        $this->razdel = $razdel;

        return $this;
    }

    public function getAvtor(): ?string
    {
        return $this->avtor;
    }

    public function setAvtor(?string $avtor): self
    {
        $this->avtor = $avtor;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }

    public function getComment(): ?int
    {
        return $this->comment;
    }

    public function setComment(int $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getArhiv(): ?\DateTimeInterface
    {
        return $this->arhiv;
    }

    public function setArhiv(\DateTimeInterface $arhiv): self
    {
        $this->arhiv = $arhiv;

        return $this;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function setHref(?string $href): self
    {
        $this->href = $href;

        return $this;
    }

    public function getSi(): ?string
    {
        return $this->si;
    }

    public function setSi(string $si): self
    {
        $this->si = $si;

        return $this;
    }

    public function getBi(): ?string
    {
        return $this->bi;
    }

    public function setBi(string $bi): self
    {
        $this->bi = $bi;

        return $this;
    }

    public function getMetka(): ?string
    {
        return $this->metka;
    }

    public function setMetka(string $metka): self
    {
        $this->metka = $metka;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }
}

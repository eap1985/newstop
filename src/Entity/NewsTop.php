<?php

namespace eap1985\NewsTopBundle\Entity;

use App\Entity\Comment;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Gedmo\Mapping\Annotation as Gedmo;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @ORM\Table(name="newstop")
 * @ORM\Entity
 * @Vich\Uploadable()
 * @Assert\Callback("validate")
 * @UniqueEntity("slug")
 * @ORM\HasLifecycleCallbacks()
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
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\OneToOne(targetEntity="eap1985\NewsTopBundle\Entity\NewsBody",cascade={"remove"},mappedBy="NewsBody")
     * @ORM\JoinColumns( { @ORM\JoinColumn(name="node", referencedColumnName="id", onDelete="CASCADE" ) } )
     */

    private $node;

    /**
     * @ORM\PostLoad
     * @ORM\PostPersist
     */
    public function fetchEntityManager(LifecycleEventArgs $args)
    {
        $this->setEntityManager($args->getEntityManager());
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
        return $this->em;
    }

    // generated getter and setter
    public function setNode(NewsBody $node = null): NewsBody
    {

        $this->node = $node;
        return $this;
    }

    public function getNode()
    {
        return $this->node;
    }


    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;
        return $this;
    }

    private $bodyValue;

    // generated getter and setter

    public function getBodyValue($created = null)
    {

        if (!empty($this->node)) {
            return $this->node->getBodyValue();
        }

        return $this->bodyValue;
    }

    // generated getter and setter
    public function setBodyValue($string = '-')
    {
        $this->bodyValue = $string;
        if (!empty($this->node)) $this->node->setBodyValue($string);
        return $this;
    }

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

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     */
    private $slug;


    /**
     *
     */
    private $image;


    public function createNode($conference, $entityManager)
    {
        $request = Request::createFromGlobals();
        $body_value = $request->request->get('body_value', 'default category');

        $this->node = new NewsBody();

        $this->node->setBundle('newstop');
        $this->node->setEntityId($conference->getId());

        $this->node->setBodyValue($conference->bodyValue);
        $this->node->setLangcode('ru');
        $this->node->setDelta(1);
        $this->node->setRevisionId(1);
        $this->node->setDeleted(0);
        // сообщите Doctrine, что вы хотите (в итоге) сохранить Продукт (пока без запросов)
        $entityManager->persist($this->node);

        // действительно выполните запросы (например, запрос INSERT)
        $entityManager->flush();
    }


    public function computeSlug(SluggerInterface $slugger)
    {


        $this->slug = (string)$slugger->slug((string)$this)->lower();

    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString()
    {
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
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
    }

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
        if ($this->pdfFile) {
            $this->updatedAt = new DateTime();
        }
    }


    /**
     * @param ExecutionContextInterface $context
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!empty($this->pdfFile)) {
            if (!in_array($this->pdfFile->getMimeType(), array(
                'application/pdf',
            ))) {
                $context
                    ->buildViolation('Wrong file type (pdf)')
                    ->atPath('pdfFile')
                    ->addViolation();
            }
        }

        if (!empty($this->thumbnailFile)) {
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


    public function setThumbnail(?string $thumbnail = 'no'): self
    {

        if (!empty($thumbnail)) {
            $this->thumbnail = $thumbnail;
        } else {
            $this->thumbnail = 'no';
        }
        return $this;
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
        if ($this->thumbnailFile) {
            $this->updatedAt = new DateTime();
        }
    }

    /**
     * @var string
     *
     * @ORM\Column(name="razdel", type="string", length=50, nullable=true)
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
     * @ORM\Column(name="count", type="integer", nullable=true)
     */
    private $count;

    /**
     * @var string|null
     *
     * @ORM\Column(name="href", type="string", length=50, nullable=true)
     */
    private $href;

    /**
     * @var string
     *
     * @ORM\Column(name="si", type="string", length=255, nullable=true)
     */
    private $si;

    /**
     * @var string
     *
     * @ORM\Column(name="bi", type="string", length=255, nullable=true)
     */
    private $bi;

    /**
     * @var string
     *
     * @ORM\Column(name="metka", type="string", length=255, nullable=true)
     */
    private $metka;

    /**
     * @var int
     *
     * @ORM\Column(name="archived",  type="boolean", nullable=false)
     */
    private $archived;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="newsTop", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="smallint", options={"default" : 0})
     */
    private $sale;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $saletype;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $attachment;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->comments = new ArrayCollection();
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

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setNewsTop($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getNewsTop() === $this) {
                $comment->setNewsTop(null);
            }
        }

        return $this;
    }

    public function getSale(): ?int
    {
        return $this->sale;
    }

    public function setSale(int $sale): self
    {
        $this->sale = $sale;

        return $this;
    }

    public function getSaletype(): ?int
    {
        return $this->saletype;
    }

    public function setSaletype(int $saletype): self
    {
        $this->saletype = $saletype;

        return $this;
    }


    public function getAttachmentSql()
    {
        $conn = $this->em->getConnection();

        $sql = '
            SELECT id FROM files f
            WHERE f.node_id  = ' . $this->getId() . '
            ORDER BY f.id DESC
            ';
        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllNumeric();

    }

    public function getAttachment(): array
    {

        $decode = json_decode($this->attachment);
        $val = [];
        $r = $this->getAttachmentSql();

        if (!empty($r)) {
            $newar = [];
            foreach ($r as $val => $item) {
                array_push($newar, $item[0]);
            }

            $val = implode(',', $newar);
        }


        return array('attachmenthidden' => $val);
    }

    public function setAttachment($attachment): self
    {
        if (!empty($attachment['attachmenthidden'])) {
            $val = explode(',', $attachment['attachmenthidden']);

            $json = json_encode(array('files' => $val));
            $this->attachment = $json;
        }
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }


    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }


    public function getCartImage(): ?string
    {
         return $this->cartimage;
    }

    public function setCartImage(string $cartimage): self
    {
        $this->cartimage = $cartimage;

        return $this;
    }





}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaFileRepository")
 * @Gedmo\Uploadable(pathMethod="getUploadPath", filenameGenerator="SHA1", allowOverwrite=true, appendNumber=true)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=false)
 */
class MediaFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"product:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=500)
     * @Groups({"product:item"})
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\UploadableFileName
     * @Groups({"product:item"})
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $originalFilename;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\UploadableFileMimeType
     */
    private $mimeType;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=0, nullable=true)
     * @Gedmo\UploadableFileSize
     */
    private $size;

    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @var string
     */
    private $uploadPath;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\Column(type="integer", options={"default" : 1})
     * @Groups({"product:item"})
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="images")
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getName(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return null|UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return MediaFile
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
//        var_dump($file);
//        var_dump($file->getClientOriginalExtension());

        if ($file instanceof UploadedFile) {
//            echo PHP_EOL.'popali';
//            die();
            $this->setUpdatedAt(new \DateTime('now'));
            $this->setFilename(
                sha1(uniqid($file->getClientOriginalName(), true)) . '.' . $file->getClientOriginalExtension()
            );
//            var_dump($file);die();
//            if ($file->getMimeType()){
//                $this->setMimeType($file->getMimeType());
//            }

            $this->setSize($file->getSize());
            $this->setOriginalFilename($file->getClientOriginalName());
        }

        return $this;
    }

    public function getWebPath()
    {
        return str_replace('./', '', $this->getPath().'/'.$this->getFilename());
    }

    /**
     * @return mixed
     */
    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    /**
     * @param string $uploadPath
     *
     * @return MediaFile
     */
    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = $uploadPath;
        $this->path = str_replace('./public/', './', $uploadPath);

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

}

<?php

namespace App\Service;

use \Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ImageService
{
  public function __construct(CacheManager $imagineCacheManager)
  {
    $this->imagineCacheManager = $imagineCacheManager;
  }

  public function getCachedImage(string $webPath, string $filter = 'thumb_1920_1080'): ?string
  {
    if ($webPath) {
      return $this->imagineCacheManager->getBrowserPath($webPath, $filter);
    }
    return null;
  }

  public function buildManyImageWithCache(array &$serialized, string $filter = 'thumb_1920_1080')
  {
    foreach ($serialized as &$serializedItem) {
      if (isset($serializedItem->filename) && isset($serializedItem->path)) {
        $serializedItem->cachedImage = $this->getCachedImage(
            str_replace('./', '', $serializedItem->path . '/' . $serializedItem->filename),
            $filter
        );
      }
    }
  }

  public function buildOneImageWithCache(object &$serializedItem, string $filter = 'thumb_1920_1080')
  {
    if ($serializedItem->image) {
      $serializedItem->image->cachedImage = $this->getCachedImage(
          str_replace('./', '', $serializedItem->image->path . '/' . $serializedItem->image->filename),
          $filter
      );
    }
  }

  public function setPhotoPath($photo, $uploadPath, $getArray)
  {
    $base64_string = $getArray->get('base64')->getData();
    $name64 = time();
    $photo->setUploadPath($uploadPath);
    $filename = sha1(uniqid($name64, true)) . '.jpg';
    if (!file_exists($uploadPath)) {
      echo "create new dir=" . $uploadPath . PHP_EOL;
      mkdir($uploadPath, 0755, true);
    }
    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );
    if(sizeof($data)>1) {
      $encoded_string = $data[1];
    } else {
      $encoded_string = $data[0];
    }
    $imgdata = base64_decode($encoded_string);
    $mimetype = self::getImageMimeType($imgdata);

    file_put_contents($uploadPath . '/' . $filename, $imgdata);

    $photo->setFilename($filename)
        ->setMimeType($mimetype)
        ->setOriginalFilename($name64);
  }

  function getBytesFromHexString($hexdata)
  {
    for ($count = 0; $count < strlen($hexdata); $count += 2)
      $bytes[] = chr(hexdec(substr($hexdata, $count, 2)));

    return implode($bytes);
  }

  function getImageMimeType($imagedata)
  {
    $imagemimetypes = array(
        "jpeg" => "FFD8",
        "png" => "89504E470D0A1A0A",
        "gif" => "474946",
        "bmp" => "424D",
        "tiff" => "4949",
        "tiff" => "4D4D"
    );

    foreach ($imagemimetypes as $mime => $hexbytes) {
      $bytes = self::getBytesFromHexString($hexbytes);
      if (substr($imagedata, 0, strlen($bytes)) == $bytes)
        return "image/".$mime;
    }

    return NULL;
  }
}
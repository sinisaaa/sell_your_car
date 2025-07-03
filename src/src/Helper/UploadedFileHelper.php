<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class UploadedFileHelper
{

    /**
     * @param array<mixed> $uploadedFiles
     */
    public static function validateImages(array $uploadedFiles): void
    {
        foreach ($uploadedFiles as $file) {
            if (!$file instanceof UploadedFile) {
                throw new BadRequestHttpException('Uploaded file is not image.');
            }

            $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF, IMAGETYPE_BMP);
            if (!\in_array(exif_imagetype($file->getRealPath()), $allowedTypes, true)) {
                throw new BadRequestHttpException('Uploaded image is not in valid format.');
            }
        }
    }

}
<?php

namespace App\Services;

use App\Entity\UserCertificate;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const CERTIFICATE_PATH = 'certificate/';
    const ROUTINE_PHOTO_PATH = 'routine/';

    private $uploadsPath;

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadFile(UploadedFile $file, string $path = UploaderHelper::CERTIFICATE_PATH): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        $filesystem = new Filesystem();
        $fileDir = $this->uploadsPath . $path;

        if (!$filesystem->exists($fileDir)) {
            $filesystem->mkdir($fileDir);
        }

        try {
            $file->move(
                $fileDir,
                $newFilename
            );
            return $newFilename;
        } catch (FileException $e) {
            throw new FileException("File was not uploaded." . $e);
        }
    }

    public function deleteСertificate(UserCertificate $certificateName): void
    {
        $filesystem = new Filesystem();
        $certificateTest = $this->uploadsPath . $certificateName->getCertificate();
        if ($filesystem->exists($certificateTest)) {
            try {
                $filesystem->remove($certificateTest);
            } catch (IOExceptionInterface $exception) {
                throw new FileException("File was not uploaded." . $exception);
            }
        }
    }

    public function deleteFile(string $fileName, string $path = UploaderHelper::CERTIFICATE_PATH): void
    {
        $filesystem = new Filesystem();
        $fileTest = $this->uploadsPath . $path . $fileName;
        if ($filesystem->exists($fileTest)) {
            try {
                $filesystem->remove($fileTest);
            } catch (IOExceptionInterface $exception) {
                throw new FileException("File was not uploaded." . $exception);
            }
        }
    }


}
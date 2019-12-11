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

    private $uploadsPath;

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    public function uploadCertificatePDF(UploadedFile $certificate): string
    {
        $originalFilename = pathinfo($certificate->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $certificate->guessExtension();

        $filesystem = new Filesystem();
        $certificateDir = $this->uploadsPath . UploaderHelper::CERTIFICATE_PATH;

        if (!$filesystem->exists($certificateDir)) {
            $filesystem->mkdir($certificateDir);
        }

        try {
            $certificate->move(
                $certificateDir,
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

}
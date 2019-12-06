<?php

namespace App\Services;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const CERTIFICATE_PATH = 'certificate/';

    private $uploadsPath;
    private $uploadsCertificatePath = '/certificate/';

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }

    static public function uploadCertificateWithId($expertId): string
    {
        $finder = new Finder();
        $dir = UploaderHelper::CERTIFICATE_PATH . $expertId;
        $finder->files()->in($dir)->name('*.pdf');

        //Todo: 404
        if (!$finder->hasResults()) {
            return '';
        }

        foreach ($finder as $file) {
            return UploaderHelper::CERTIFICATE_PATH . $expertId . '/' . $file->getFilename();
        }

        return '';
    }

    public function uploadCertificatePDF(UploadedFile $certificate): string
    {
        $originalFilename = pathinfo($certificate->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $certificate->guessExtension();

        $filesystem = new Filesystem();
        $certificateDir = getcwd() . $this->uploadsCertificatePath;

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
            throw new FileException("Файл небыл загружен. " . $e);
        }
    }

    public function deleteСertificate($certificateName): void
    {
        $filesystem = new Filesystem();
        $certificateTest = getcwd() . $this->uploadsCertificatePath . $certificateName;
        if ($filesystem->exists($certificateTest)) {
            try {
                $filesystem->remove($certificateTest);
            } catch (IOExceptionInterface $exception) {
                throw new FileException("Файл небыл загружен. " . $exception);
            }
        }
    }


}
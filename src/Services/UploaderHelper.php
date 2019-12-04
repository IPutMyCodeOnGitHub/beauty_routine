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
    private $uploadsCertificatePath = '/certificate';

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

    public function uploadCertificatePDF(UploadedFile $certificate): void
    {
        $originalFilename = pathinfo($certificate->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $certificate->guessExtension();

        $filesystem = new Filesystem();
        $certificateDir = getcwd() . $this->uploadsCertificatePath . "/test";

        if (!$filesystem->exists($certificateDir)) {
            $filesystem->mkdir($certificateDir);
        }

        try {
            $certificate->move(
                $certificateDir,
                $newFilename
            );
        } catch (FileException $e) {
            throw new FileException("Файл небыл загружен. " . $e);
        }
    }

    public function renameDirСertificate($userId): void
    {
        $filesystem = new Filesystem();
        $certificateTestDir = getcwd() . $this->uploadsCertificatePath . "/test";
        $certificateDir = getcwd() . $this->uploadsCertificatePath . "/$userId";
        if (!$filesystem->exists($certificateDir) && $filesystem->exists($certificateTestDir)) {
            $filesystem->rename($certificateTestDir, $certificateDir);
        }
    }

    public function deleteDirСertificate(): void
    {
        $filesystem = new Filesystem();
        $certificateTestDir = getcwd() . $this->uploadsCertificatePath . "/test";
        if ($filesystem->exists($certificateTestDir)) {
            try {
                $filesystem->remove($certificateTestDir);
            } catch (IOExceptionInterface $exception) {
                throw new FileException("Файл небыл загружен. " . $exception);
            }
        }
    }


}
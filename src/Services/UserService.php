<?php


namespace App\Services;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function saveForm(Form $form, User $user, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        if (array_key_exists('certificate', $form->all())) {
            if ($form['certificate']->getData() !== null) {
                $certificate = $form['certificate']->getData();
                $this->saveСertificate($certificate, $user->getId());
            }
        }

        $this->manager->persist($user);
        try {
            $this->manager->flush();
            $this->renameDirСertificate($user->getId());
        } catch(\Exception $e) {
            $this->deleteDirСertificate();
        }

    }
    private function renameDirСertificate($userId): void
    {
        $filesystem = new Filesystem();
        $certificateTestDir = getcwd() . "/certificate/test";
        $certificateDir = getcwd() . "/certificate/$userId";
        if (!$filesystem->exists($certificateDir) && $filesystem->exists($certificateTestDir)) {
            $filesystem->rename($certificateTestDir, $certificateDir);
        }
    }

    private function deleteDirСertificate()
    {
        $filesystem = new Filesystem();
        $certificateTestDir = getcwd() . "/certificate/test";
        if ($filesystem->exists($certificateTestDir)) {
            try {
                $filesystem->remove($certificateTestDir);
            } catch (IOExceptionInterface $exception) {
                throw new FileException("Файл небыл загружен. " . $exception);
            }
        }
    }

    private function saveСertificate($certificate, $userId){
        $originalFilename = pathinfo($certificate->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $certificate->guessExtension();

        $filesystem = new Filesystem();
        $certificateDir = getcwd() . "/certificate/test";

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
}
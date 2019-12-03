<?php


namespace App\Services;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function createForm(AbstractType $type)
    {

    }

    public function saveForm(Form $form, $user, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        if (array_key_exists('sertificate', $form->all())) {
            $sertificate = $form['sertificate']->getData();
            $this->saveSertificate($sertificate);
        }

        $this->manager->persist($user);
        $this->manager->flush();
    }

    private function saveSertificate($sertificate){
        $originalFilename = pathinfo($sertificate->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $sertificate->guessExtension();

//        $filesystem = new Filesystem();
//        dump($filesystem->exists('../../templates/profile')); die;

        // Move the file to the directory where sertificate are stored
        //Todo:move to servise
        try {
//                $sertificate->move(
//                    $this->getParameter('brochures_directory'),
//                    $newFilename
//                );
        } catch (FileException $e) {

        }

        // Todo: сохранение файла
    }
}
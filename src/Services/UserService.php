<?php


namespace App\Services;


use Doctrine\Common\Persistence\ObjectManager;

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

    public function saveForm($form)
    {
        $form->setPassword(
            $form->encodePassword(
                $form,
                $form->get('plainPassword')->getData()
            )
        );

        $sertificate = $form['sertificate']->getData();
        if ($sertificate) {
            $this->saveSertificate($sertificate);
        }

        $this->manager->persist($form);
        $this->manager->flush();
    }

    private function saveSertificate($sertificate){
        $originalFilename = pathinfo($sertificate->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $sertificate->guessExtension();

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
<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile-expert")
 */
class ProfileExpertController extends AbstractController
{
    /**
     * @Route("", name="profile.expert")
     */
    public function index()
    {
        /** @var User $user */
        $user = $this->getUser();
        $userId = $user->getId();

        $filesystem = new Filesystem();
        $path = "/certificate/$userId";
        $certificateDir = getcwd() . $path;
        if ($filesystem->exists($certificateDir)) {
            $finder = new Finder();
            $finder->files()->in($certificateDir);

            $filesPaths = [];
            if ($finder->hasResults()) {
                foreach ($finder as $file) {
                    $filesPaths[] = $path .'/' . $file->getRelativePathname();
                }
            }
        }
        return $this->render('profile-expert/profile-expert.html.twig', [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'id' => 'userId = ' . $userId,
            'certificatesPaths' => $filesPaths,
        ]);
    }

    /**
     * @Route("/edit", name="profile.expert.edit")
     */
    public function profileExpertEdit()
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('profile-expert/profile-expert-edit.html.twig', [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }
}

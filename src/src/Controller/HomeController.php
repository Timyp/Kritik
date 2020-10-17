<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use App\Service\AppVersionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this->render('home/home.html.twig');
    }

    /**
     * #Route("/test/{id}", name="test", requirements={"id" = "\d+"})
     * @Route("/test", name="test")
     * @param ArtistRepository $repository
     */
    public function test(ArtistRepository $repository)
    {
        /**
         * findAll() permet de récuperer toutes les entités.  return array
         * find by récuperer des entités selon des critères return array
         * findOneBy récuperer une seul entité selon des critères return object ou null
         * find récuperer une entité par sa primary key return object or null
         */
        $resultat = $repository->find(34);
        dd($resultat);
    }
}

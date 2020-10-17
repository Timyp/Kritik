<?php

namespace App\Controller;

use App\Entity\Record;
use App\Repository\ArtistRepository;
use App\Repository\RecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(RecordRepository $repository)
    {
        //Get the lastest
        $lastRecords = $repository->findBy([], ['releasedAt' => 'desc'], 4);

        return $this->render('home/home.html.twig', [
            'lastRecords' => $lastRecords,
        ]);
    }

    /**
     * #Route("/test/{id}", name="test", requirements={"id" = "\d+"})
     * @Route("/test", name="test")
     * @param ArtistRepository $repository
     */
//    public function test(ArtistRepository $repository)
//    {
        /**
         * findAll() permet de récuperer toutes les entités.  return array
         * find by récuperer des entités selon des critères return array
         * findOneBy récuperer une seul entité selon des critères return object ou null
         * find récuperer une entité par sa primary key return object or null
         */
//        $resultat = $repository->find(34);
//        dd($resultat);
//    }
}

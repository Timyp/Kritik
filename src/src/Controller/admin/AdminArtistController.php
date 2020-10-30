<?php
namespace App\Controller\admin;


use App\Entity\Artist;
use App\Form\ArtistFormType;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class AdminArtistController extends AbstractController
{

    /**
     * @Route("_artist", name="artist")
     * @param ArtistRepository $artistRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(ArtistRepository $artistRepository, Request $request, PaginatorInterface $paginator)
    {
        //Get artists list
        $artist = array_reverse($artistRepository->findAll());
        $pagination = $paginator->paginate(
            $artist,
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('admin/dashboard_artist_index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/artist_form/{id}", name="artist_form")
     * @param Artist $artist
     * @param ArtistRepository $artistRepository
     * @param Request $request
     * @param EntityManagerInterface $manager
     */
    public function modifyArtist(
        Artist $artist,
        ArtistRepository $artistRepository,
        Request $request,
        EntityManagerInterface $manager
    ){
        //Récupération du label
        $currentArtist = $artistRepository->findOneBy([
            'id' => $artist->getId(),
        ]);

        //Création du formulaire
        $form = $this->createForm(ArtistFormType::class, $currentArtist);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($currentArtist);
            $manager->flush();

                $this->addFlash('success', 'L\'artiste à bien été modifiée.');
            return $this->redirectToRoute('admin_artist');
        }

        return $this->render('admin/dashboard_artist_modify.html.twig', [
            'artist_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create_artist", name="create_artist")
     * @param ArtistRepository $artistRepository
     * @param Request $request
     * @param EntityManagerInterface $manager
     */
    public function createArtist(
        ArtistRepository $artistRepository,
        Request $request,
        EntityManagerInterface $manager
    ){
        //Création du formulaire
        $form = $this->createForm(ArtistFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $artist = $form->getData();
            $manager->persist($artist);
            $manager->flush();
            $this->addFlash('success', 'Votre artiste a bien été créé.');
            return $this->redirectToRoute('admin_artist');
        }

        return $this->render('admin/dashboard_artist_modify.html.twig', [
            'artist_form' => $form->createView(),
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Label;
use App\Entity\Note;
use App\Entity\Record;
use App\Form\NoteFormType;
use App\Repository\ArtistRepository;
use App\Repository\NoteRepository;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecordController extends AbstractController
{
    /**
     * @Route("/artiste-list", name="artiste_list")
     */
    public function index(ArtistRepository $repository)
    {
       return $this->render('record/artist_list.html.twig', [
           "artist_list" => $repository->findAll(),
       ]);
    }

    /**
     * @Route("/artiste/{id}", name="artiste_page")
     */
    public function artistPage(Artist $artist)
    {
        return $this->render('record/artist_page.html.twig', [
            'artist' => $artist,
        ]);
    }

    /**
     * @Route("/label/{id}", name="label_page")
     */
    public function labelPage(Label $label)
    {
        return $this->render('record/label_page.html.twig', [
            'label' => $label,
        ]);
    }

    /**
     * @Route("/record/{id}", name="record_page")
     */
    public function recordPage(
        Record $record,
        NoteRepository $repository,
        Request $request,
        EntityManagerInterface $manager
    ){
        //Recherche d'une note existante
        if(null !== $this->getUser()){
            $note = $repository->findOneBy([
               'author' => $this->getUser(),
               'record' => $record
            ]);
        }

        //Si la note n'existe pas on en crée une nouvelle
        $note ??= (new Note())
            ->setAuthor($this->getUser())
            ->setRecord($record);

        //Création du formulaire
        $form = $this->createForm(NoteFormType::class, $note);
        $form->handleRequest($request);

        if($this->getUser() !== null && $form->isSubmitted() && $form->isValid()) {
            $manager->persist($note);
            $manager->flush();

            $this->addFlash('success', 'Votre note a été enregistrée.');
        }


        return $this->render('record/record_page.html.twig', [
            'record' => $record,
            'note_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/news", name="record_news")
     */
    public function recordNews(RecordRepository $repository)
    {
        return $this->render('record/record_news.html.twig', [
            'record_news' => $repository->getNews(),
        ]);
    }

    /**
     * @Route("/note/{id}/delete/{token}", name="record_note_delete")
     * @IsGranted("NOTE_DELETE", subject="note")
     */
    public function deleteRecordNote(Note $note, string $token, EntityManagerInterface $manager)
    {
        //Vérification du jeton CSRF
        if(false === $this->isCsrfTokenValid('record_note_delete', $token)) {
            $this->addFlash('Warning', 'Jeton invalide.');
            return $this->redirectToRoute('record_page', ['id' => $note->getRecord()->getId()]);
        }

        //Remove note
        $manager->remove($note);
        $manager->flush();
        $this->addFlash('info', 'Votre note a bien été supprimée.');
        return $this->redirectToRoute('record_page', ['id' => $note->getRecord()->getId()]);
    }

}

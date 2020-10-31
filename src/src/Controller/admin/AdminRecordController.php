<?php


namespace App\Controller\admin;


use App\Entity\Record;
use App\Form\RecordFormType;
use App\Repository\RecordRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/admin", name="admin_")
 */
class AdminRecordController extends AbstractController
{
    /**
     * @Route("_record", name="record")
     * @param RecordRepository $recordRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     */
    public function index(
        RecordRepository $recordRepository,
        Request $request,
        PaginatorInterface $paginator
    ){
        //Get records list
        $labels = array_reverse($recordRepository->findAll());

//        foreach($labels as $label) {
//            dd($label->getArtist()->getName());
//        }

        $pagination = $paginator->paginate(
            $labels,
            $request->query->getInt('page', 1),
            10
        );


        return $this->render('admin/dashboard_record_index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/record_form/{id}", name="record_form")
     * @param Record $record
     * @param RecordRepository $repository
     * @param Request $request
     * @param EntityManagerInterface $manager
     */
    public function modifyRecord(
        Record $record,
        RecordRepository $repository,
        Request $request,
        EntityManagerInterface $manager
    ){

        //Récupération du label
        $currentRecord = $repository->findOneBy([
            'id' => $record->getId(),
        ]);

        //Création du formulaire
        $form = $this->createForm(RecordFormType::class, $currentRecord);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($currentRecord);
            $manager->flush();

            $this->addFlash('success', 'Le disque à bien été modifié.');
            return $this->redirectToRoute('admin_record');
        }

        return $this->render('admin/dashboard_record_modify.html.twig', [
            'record_form' => $form->createView(),
        ]);
    }
}
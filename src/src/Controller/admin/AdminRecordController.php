<?php


namespace App\Controller\admin;


use App\Repository\RecordRepository;
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
}
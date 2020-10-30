<?php


namespace App\Controller\admin;


use App\Entity\Label;
use App\Form\LabelFormType;
use App\Repository\LabelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("", name="dashboard")
     * @param LabelRepository $labelRepository
     * @return Response
     */
    public function index(LabelRepository $labelRepository, Request $request, PaginatorInterface $paginator)
    {

        return $this->render('admin/dashboard.html.twig');
    }


}
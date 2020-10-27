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
        //Get list of all label
        $labels = array_reverse($labelRepository->findAll());
        $pagination = $paginator->paginate(
            $labels, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );


        return $this->render('admin/dashboard.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/label_form/{id}", name="label_form")
     * @param Label $label
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function modifyLabel(
        Label $label,
        LabelRepository $repository,
        Request $request,
        EntityManagerInterface $manager
    ){

        //Récupération du label
        $currentLabel = $repository->findOneBy([
            'id' => $label->getId(),
        ]);

        //Création du formulaire
        $form = $this->createForm(LabelFormType::class, $currentLabel);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($currentLabel);
            $manager->flush();

            $this->addFlash('success', 'Le label à bien été modifiée.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/dashboard_label.html.twig', [
            'label_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/create_label", name="create_label")
     * @param LabelRepository $repository
     * @param Request $request
     * @param EntityManagerInterface $manager
     */
    public function createLabel(
        LabelRepository $repository,
        Request $request,
        EntityManagerInterface $manager
    ){
        //Création du formulaire
        $form = $this->createForm(LabelFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $label = $form->getData();
            $manager->persist($label);
            $manager->flush();
            $this->addFlash('success', 'Votre label a bien été créé.');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/dashboard_create_label.html.twig', [
            'label_form' => $form->createView(),
        ]);
    }
}
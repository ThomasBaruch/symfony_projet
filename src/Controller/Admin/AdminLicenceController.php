<?php

namespace App\Controller\Admin;

use App\Entity\Licence;
use App\Form\LicenceType;
use App\Repository\LicenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminLicenceController extends AbstractController
{
    /**
     *@Route("/admin/licences", name="admin_licence_list")
     */
    public function listLicence(LicenceRepository $licenceRepository)
    {
        $licences = $licenceRepository->findAll();

        return $this->render("admin/licences.html.twig", ['licences' => $licences]);
    }


    /**
     * @Route("admin/licence/{id}", name="admin_show_licence")
     */
    public function showLicence(LicenceRepository $licenceRepository, $id)
    {
        $licence = $licenceRepository->find($id);

        return $this->render("admin/licence.html.twig", ['licence' => $licence]);
    }

    /**
     * @Route("/admin/search/", name="admin_search")
     */
    public function adminSearch(LicenceRepository $licenceRepository, Request $request)
    {
        $term = $request->query->get('term');

        $licences = $licenceRepository->searchByTerm($term);

        return $this->render('admin/search.html.twig', ['licences' => $licences]);
    }


    /**
     * @Route("admin/add/licence/", name="admin_licence_add")
     */
    public function createLicence(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $licence = new Licence();

        $licenceForm = $this->createForm(LicenceType::class, $licence);

        $licenceForm->handleRequest($request);

        if($licenceForm->isSubmitted() && $licenceForm->isValid()){

            $licenceFile = $licenceForm->get('media')->getData();

            if($licenceFile){
                $originalFilename = pathinfo($licenceFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $sluggerInterface->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' .$licenceFile->guessExtension();
                $licenceFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $licence->setMedia($newFilename);
            }

            $entityManagerInterface->persist($licence);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_licence_list");

        }

        return $this->render('admin/licenceupdate.html.twig', ['licenceForm' => $licenceForm->createView()]);
    }


    /**
     * @Route("admin/update/licence/{id}", name="admin_licence_update")
     */
    public function licencetUpdate(
        $id,
        LicenceRepository $licenceRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {
        $licence = $licenceRepository->find($id);

        $licenceForm = $this->createForm(LicenceType::class, $licence);

        $licenceForm->handleRequest($request);

        if ($licenceForm->isSubmitted() && $licenceForm->isValid()) {
            $entityManagerInterface->persist($licence);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_licence_list');
        }

        return $this->render('admin/licenceupdate.html.twig', ['licenceForm' => $licenceForm->createView()]);
    }


    /**
     * @Route("admin/delete/licence/{id}", name="admin_licence_delete")
     */
    public function deleteLicence($id, LicenceRepository $licenceRepository, EntityManagerInterface $entityManagerInterface)
    {
        $licence = $licenceRepository->find($id);
        $entityManagerInterface->remove($licence);
        $entityManagerInterface->flush();
        $this->addFlash(
            'notice',
            'Votre licence a été supprimé'
        );

        return $this->redirectToRoute("admin_licence_list");
    }
}


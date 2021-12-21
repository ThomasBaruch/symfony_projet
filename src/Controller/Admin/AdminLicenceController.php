<?php

namespace App\Controller\Admin;

use App\Entity\Licence;
use App\Form\LicenceType;
use App\Repository\LicenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminLicenceController extends AbstractController
{
    /**
     * @Route("/admin/licences/", name="admin_list_licence")
     */
    public function listLicence(LicenceRepository $licenceRepository)
    {
        $licences = $licenceRepository->findAll();

        return $this->render("admin/licences.html.twig", ['licences' => $licences]);
    }

    /**
     * @Route("admin/licence/{id}", name="admin_show_licence")
     */
    public function showLicence($id, LicenceRepository $licenceRepository)
    {
        $licence = $licenceRepository->find($id);

        return $this->render("admin/licence.html.twig", ['licence' => $licence]);
    }

    /**
     * @Route("admin/update/licence/{id}", name="admin_update_licence")
     */
    public function updateLicence(
        $id,
        LicenceRepository $licenceRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        SluggerInterface $sluggerInterface
    ) {
        $licence = $licenceRepository->find($id);

        $licenceForm = $this->createForm(LicenceType::class, $licence);

        $licenceForm->handleRequest($request);

        if ($licenceForm->isSubmitted() && $licenceForm->isValid()) {

            $mediaFile = $licenceForm->get('media')->getData();

            if ($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $licence->setMedia($newFilename);
            }

            $entityManagerInterface->persist($licence);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_licence");
        }

        return $this->render("front/licenceform.html.twig", ['licenceForm' => $licenceForm->createView()]);
    }

    /**
     * @Route("admin/create/licence/", name="admin_create_licence")
     */
    public function createLicence(
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        SluggerInterface $sluggerInterface
    ) {
        $licence = new Licence();

        $licenceForm = $this->createForm(LicenceType::class, $licence);

        $licenceForm->handleRequest($request);

        if ($licenceForm->isSubmitted() && $licenceForm->isValid()) {

            $mediaFile = $licenceForm->get('media')->getData();

            if ($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $licence->setMedia($newFilename);
            }

            $entityManagerInterface->persist($licence);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_licence");
        }

        return $this->render("admin/licenceform.html.twig", ['licenceForm' => $licenceForm->createView()]);
    }

    /**
     * @Route("/admin/delete/{id}", name="admin_delete_licence")
     */
    public function deleteLicence($id, EntityManagerInterface $entityManagerInterface, LicenceRepository $licenceRepository)
    {
        $licence = $licenceRepository->find($id);

        $entityManagerInterface->remove($licence);
        $entityManagerInterface->flush();

        return $this->redirectToRoute("admin_list_licence");
    }
}
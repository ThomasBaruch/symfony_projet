<?php

namespace App\Controller\Front;

use App\Repository\LicenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LicenceController extends AbstractController
{

    /**
     * @Route("/front/licences/", name="front_list_licence")
     */
    public function listLicence(LicenceRepository $licenceRepository)
    {
        $licences = $licenceRepository->findAll();

        return $this->render("front/licences.html.twig", ['licences' => $licences]);
    }

    /**
     * @Route("front/licence/{id}", name="front_show_licence")
     */
    public function showLicence($id, LicenceRepository $licenceRepository)
    {
        $licence = $licenceRepository->find($id);

        return $this->render("front/licence.html.twig", ['licence' => $licence]);
    }
}
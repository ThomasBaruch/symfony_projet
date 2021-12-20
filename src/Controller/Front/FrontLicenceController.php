<?php

namespace App\Controller\Front;

use App\Repository\LicenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontLicenceController extends AbstractController
{
    /**
     *@Route("/front/licences", name="front_licence_list")
     */
    public function listLicence(LicenceRepository $licenceRepository)
    {
        $licences = $licenceRepository->findAll();

        return $this->render("front/licences.html.twig", ['licences' => $licences]);
    }


    /**
     * @Route("front/licence/{id}", name="front_show_licence")
     */
    public function showLicence(LicenceRepository $licenceRepository, $id)
    {
        $licence = $licenceRepository->find($id);

        return $this->render("front/licence.html.twig", ['licence' => $licence]);
    }

    /**
     * @Route("/front/search/", name="front_search")
     */
    public function frontSearch(LicenceRepository $licenceRepository, Request $request)
    {
        $term = $request->query->get('term');

        $licences = $licenceRepository->searchByTerm($term);

        return $this->render('front/search.html.twig', ['licences' => $licences]);
    }
}

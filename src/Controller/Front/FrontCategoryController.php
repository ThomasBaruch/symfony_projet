<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontCategoryController extends AbstractController
{
    /**
     *@Route("/front/categorys", name="front_category_list")
     */
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categorys = $categoryRepository->findAll();

        return $this->render("front/categorys.html.twig", ['categorys' => $categorys]);
    }


    /**
     * @Route("front/category/{id}", name="front_show_category")
     */
    public function showCategory(CategoryRepository $categoryRepository, $id)
    {
        $category = $categoryRepository->find($id);

        return $this->render("front/category.html.twig", ['category' => $category]);
    }

    /**
     * @Route("/front/search/", name="front_search")
     */
    public function frontSearch(CategoryRepository $categoryRepository, Request $request)
    {
        $term = $request->query->get('term');

        $categorys = $categoryRepository->searchByTerm($term);

        return $this->render('front/search.html.twig', ['categorys' => $categorys]);
    }
}

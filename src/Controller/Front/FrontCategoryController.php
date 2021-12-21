<?php

namespace App\Controller\Front;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{

    /**
     * @Route("/front/categories/", name="front_list_category")
     */
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render("front/categories.html.twig", ['categories' => $categories]);
    }

    /**
     * @Route("front/category/{id}", name="front_show_category")
     */
    public function showCategory(CategoryRepository $categoryRepository, $id)
    {
        $category = $categoryRepository->find($id);

        return $this->render("front/category.html.twig", ['category' => $category]);
    }
}
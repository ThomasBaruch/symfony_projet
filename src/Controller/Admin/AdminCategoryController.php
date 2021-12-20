<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCategoryController extends AbstractController
{
    /**
     *@Route("/admin/categorys", name="admin_category_list")
     */
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categorys = $categoryRepository->findAll();

        return $this->render("admin/categorys.html.twig", ['categorys' => $categorys]);
    }


    /**
     * @Route("admin/category/{id}", name="admin_show_category")
     */
    public function showCategory(CategoryRepository $categoryRepository, $id)
    {
        $category = $categoryRepository->find($id);

        return $this->render("admin/category.html.twig", ['category' => $category]);
    }

    /**
     * @Route("/admin/search/", name="admin_search")
     */
    public function adminSearch(CategoryRepository $categoryRepository, Request $request)
    {
        $term = $request->query->get('term');

        $categorys = $categoryRepository->searchByTerm($term);

        return $this->render('admin/search.html.twig', ['categorys' => $categorys]);
    }


    /**
     * @Route("admin/add/category/", name="admin_category_add")
     */
    public function createCategory(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if($categoryForm->isSubmitted() && $categoryForm->isValid()){

            $categoryFile = $categoryForm->get('media')->getData();

            if($categoryFile){
                $originalFilename = pathinfo($categoryFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $sluggerInterface->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' .$categoryFile->guessExtension();
                $categoryFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $category->setMedia($newFilename);
            }

            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_category_list");

        }

        return $this->render('admin/categoryupdate.html.twig', ['categoryForm' => $categoryForm->createView()]);
    }


    /**
     * @Route("admin/update/category/{id}", name="admin_category_update")
     */
    public function categorytUpdate(
        $id,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {
        $category = $categoryRepository->find($id);

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_category_list');
        }

        return $this->render('admin/categoryupdate.html.twig', ['categoryForm' => $categoryForm->createView()]);
    }


    /**
     * @Route("admin/delete/category/{id}", name="admin_category_delete")
     */
    public function deleteCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManagerInterface)
    {
        $category = $categoryRepository->find($id);
        $entityManagerInterface->remove($category);
        $entityManagerInterface->flush();
        $this->addFlash(
            'notice',
            'Votre category a été supprimé'
        );

        return $this->redirectToRoute("admin_category_list");
    }
}
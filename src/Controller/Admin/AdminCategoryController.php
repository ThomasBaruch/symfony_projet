<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCategoryController extends AbstractController
{
    /**
     * @Route("/admin/categories/", name="admin_list_category")
     */
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();

        return $this->render("admin/categories.html.twig", ['categories' => $categories]);
    }

    /**
     * @Route("admin/category/{id}", name="admin_show_category")
     */
    public function showCategory($id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        return $this->render("admin/category.html.twig", ['category' => $category]);
    }

    /**
     * @Route("admin/update/category/{id}", name="admin_update_category")
     */
    public function updateCategory(
        $id,
        CategoryRepository $categoryRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        SluggerInterface $sluggerInterface
    ) {
        $category = $categoryRepository->find($id);

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            $mediaFile = $categoryForm->get('media')->getData();

            if ($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $category->setMedia($newFilename);
            }

            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_category");
        }

        return $this->render("admin/categoryform.html.twig", ['categoryForm' => $categoryForm->createView()]);
    }

    /**
     * @Route("admin/create/category/", name="admin_create_category")
     */
    public function createCategory(
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        SluggerInterface $sluggerInterface
    ) {
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            $mediaFile = $categoryForm->get('media')->getData();

            if ($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $category->setMedia($newFilename);
            }

            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_category");
        }

        return $this->render("admin/categoryform.html.twig", ['categoryForm' => $categoryForm->createView()]);
    }

    /**
     * @Route("/admin/delete/{id}", name="admin_delete_category")
     */
    public function deleteCategory($id, EntityManagerInterface $entityManagerInterface, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        $entityManagerInterface->remove($category);
        $entityManagerInterface->flush();

        return $this->redirectToRoute("admin_list_category");
    }
}
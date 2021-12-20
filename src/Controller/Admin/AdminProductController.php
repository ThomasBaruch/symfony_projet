<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminProductController extends AbstractController
{
    /**
     *@Route("/admin/products", name="admin_product_list")
     */
    public function listProduct(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render("admin/products.html.twig", ['products' => $products]);
    }


    /**
     * @Route("admin/product/{id}", name="admin_show_product")
     */
    public function showProduct(ProductRepository $productRepository, $id)
    {
        $product = $productRepository->find($id);

        return $this->render("admin/product.html.twig", ['product' => $product]);
    }

    /**
     * @Route("/admin/search/", name="admin_search")
     */
    public function adminSearch(ProductRepository $productRepository, Request $request)
    {
        $term = $request->query->get('term');

        $products = $productRepository->searchByTerm($term);

        return $this->render('admin/search.html.twig', ['products' => $products]);
    }


    /**
     * @Route("/admin/add/product/", name="admin_add_product")
     */
    public function adminCreateProduct(EntityManagerInterface $entityManagerInterface, Request $request)
    {
        $product = new Product();

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {

            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();
            $this->addFlash('notice', 'Votre produit a été créé.');

            return $this->redirectToRoute('admin_product_list');
        }

        return $this->render('admin/product_add.html.twig', ['productForm' => $productForm->createView()]);
    }



    /**
     * @Route("/admin/update/product/{id}", name="admin_update_product")
     */
    public function adminUpdateProduct(
        $id,
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        ProductRepository $productRepository
    ) {

        $product = $productRepository->find($id);

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {

            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();
            $this->addFlash('notice', 'Le produit a été modifié.');

            return $this->redirectToRoute('admin_product_list');
        }

        return $this->render('admin/product_add.html.twig', ['productForm' => $productForm->createView()]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminProductController extends AbstractController
{
    /**
     * @Route("admin/products/", name="admin_list_product")
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
     * @Route("admin/update/product/{id}", name="admin_upadte_product")
     */
    public function updateProduct(
        $id,
        EntityManagerInterface $entityManagerInterface,
        ProductRepository $productRepository,
        Request $request
    ) {
        $product = $productRepository->find($id);

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_product");
        }

        return $this->render("admin/productform.html.twig", ['productForm' => $productForm->createView()]);
    }

    /**
     * @Route("admin/create/product/", name="admin_create_product")
     */
    public function createProduct(
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {
        $product = new Product();

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_product");
        }

        return $this->render("admin/productform.html.twig", ['productForm' => $productForm->createView()]);
    }

    /**
     * @Route("admin/delete/product/{id}", name="admin_delete_product")
     */
    public function deleteProduct(
        $id,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManagerInterface
    ) {

        $product = $productRepository->find($id);

        $entityManagerInterface->remove($product);

        $entityManagerInterface->flush();

        return $this->redirectToRoute("admin_list_product");
    }
}
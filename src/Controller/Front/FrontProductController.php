<?php

namespace App\Controller\Front;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("front/products/", name="front_list_product")
     */
    public function listProduct(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render("front/products.html.twig", ['products' => $products]);
    }

    /**
     * @Route("front/product/{id}", name="front_show_product")
     */
    public function showProduct(ProductRepository $productRepository, $id)
    {
        $product = $productRepository->find($id);

        return $this->render("front/product.html.twig", ['product' => $product]);
    }
}
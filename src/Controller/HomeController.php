<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	/**
	 * @Route("/", name="homepage")
	 * @param ProductRepository $productRepository
	 *
	 */
	public function homepage(ProductRepository $productRepository)
	{
		// count([])
		// find(id)
		// findBy([critère de recherche], [critère de tri], limite (int)) (Renvoie un tableau)
		// findOneBy([critère de recherche])
		// findAll() Renvoie TOUS les produits

		// $products = $productRepository->findBy([
		// 	'slug' => 'table-en-plastique'
		// ], [
		// 	'price' => 'DESC'
		// ]);
		//
		// dump($products);

		return $this->render('home.html.twig');
	}
}
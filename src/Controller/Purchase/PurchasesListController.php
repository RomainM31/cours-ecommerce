<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PurchasesListController extends AbstractController
{
	/**
	 * @Route("/purchases", name="purchase_index")
	 * @IsGranted("ROLE_USER", message="Vous devez être connecté pour accéder à vos commandes.")
	 */
	public function index()
	{
		// 1. Nous devons nous assurer que l'utilisateur est connecté. Sinon le rediriger vers la page d'accueil. => Security
		/** @var User */
		$user = $this->getUser();
		// 2. Nous voulons IDENTIFIER quel utilisateur est connecté. => Security
		// 3. Nous voulons passer l'utilisateur connecté à Twig afin d'afficher ses commandes. => Environnement Twig
		return $this->render('purchase/index.html.twig', [
			'purchases' => $user->getPurchases()
		]);

	}
}
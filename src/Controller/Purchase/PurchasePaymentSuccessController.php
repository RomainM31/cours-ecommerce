<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     * @param $id
     * @param PurchaseRepository $purchaseRepository
     * @param EntityManagerInterface $em
     * @param CartService $cartService
     * @param EventDispatcherInterface $dispatcher
     * @return RedirectResponse
     */
	public function success($id, PurchaseRepository $purchaseRepository,
                            EntityManagerInterface $em, CartService $cartService, EventDispatcherInterface $dispatcher)
	{
		// 1. Je récupère la commande
		$purchase = $purchaseRepository->find($id);

		// Si je n'ai pas de commande ou si je ne suis pas l'utilisateur lié à cette commande, ou si la commande est déjà payée :
		if (
			!$purchase ||
			($purchase && $purchase->getUser() !== $this->getUser()) ||
			($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
		) {
			$this->addFlash('warning', "La commande n'existe pas");
			return $this->redirectToRoute("purchase_index");
		}

		// 2. Je la fait passer au statut payé (PAID)
		$purchase->setStatus(Purchase::STATUS_PAID);
		$em->flush();

		// 3. Je vide le panier
		$cartService->empty();

		// 3.5 Lancer un événement qui permettra aux autres développeurs de réagir à la prise de commande.
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent,'purchase.success');

		// 4. Je redirige avec un flash vers la liste des commandes
		$this->addFlash('success', "La commande a été confirmée et payée !");
		return $this->redirectToRoute("purchase_index");
	}
}
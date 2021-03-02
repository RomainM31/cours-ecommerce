<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController extends AbstractController
{
    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }


    /**
     * @Route("/hello/{prenom?World}", name="hello")
     * @param string $prenom
     * @param LoggerInterface $logger
     * @param Calculator $calculator
     * @param Slugify $slugify
     * @param Environment $twig
     *
     * @return Response
     */
    public function hello($prenom = "World", LoggerInterface $logger, Calculator $calculator, Slugify $slugify, Environment $twig): Response
    {
        dump($twig);

        dump($slugify->slugify("Hello World"));

        $logger->error("Mon message de log !");

        $tva = $calculator->calcul(100);

        dump($tva);

        return new Response("Bonjour $prenom !");
    }
}

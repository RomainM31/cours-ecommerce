<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// TOUT nos controllers, TOUTES nos fonctions doivent TOUJOURS retourner une réponse
class TestController
{
    public function index(): Response
    {

        dd("Ca fonctionne");
    }

    // En passant les paramètres $age et $prenom à ma fonction, je peux me passer des récupération des attributs de la request !

    /**
     * @Route("/test/{age<\d+>?28}/{prenom<\w+>?Romain}", name="test", methods={"GET", "POST"}, host="localhost", schemes={"http","https"})
     * @param Request $request
     * @param $age
     * @param $prenom
     * @return Response
     */
    public function test(Request $request, $age, $prenom): Response
    {
//        $age = $request->attributes->get('age', 29);
//        $prenom = $request->attributes->get('prenom', 'Le Bib');

        return new Response("Vous êtes $prenom, et vous avez $age ans");
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class IndexController extends AbstractController
{

    /**
     * @Route("/", name="home_FE")
     * @Route("/{route}", name="vue_pages", requirements={"route"="^(?!.*_wdt|_profiler|api|test|pass_recovery_report).+"})
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request): Response
    {
        $session = $request->getSession();
        return $this->render('base.html.twig', []);
    }
}

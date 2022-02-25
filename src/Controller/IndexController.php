<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\MailerService;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }

    /**
     * @Route("/change_locale/{locale}", name="change_locale")
     */
    public function changeLocale($locale, Request $request)
    {
        $request->getSession()->set('_locale', $locale);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/send_email", name="send_email")
     */
    public function sendEmail(Request $request, MailerService $mailerService)
    {
        parse_str($request->request->get('form'), $form);

        $mailer = $mailerService->sendEmail($form,
            $this->getParameter('mailer.sender'),
            $this->getParameter('mailer.recipient')
        );

        return new JsonResponse([
            'success' => $mailer,
            'message' => ($mailer === true) ? 'Votre message a bien été envoyé!' : 'Un problème est survenu!',
            'class'   => ($mailer === true) ? 'text-success' : 'text-danger'
        ]);
    }

}

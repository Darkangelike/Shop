<?php

namespace App\Controller;

use App\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'profile')]
    public function index(Request $request): Response
    {
        $address = new AddressType();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            
        }


        return $this->renderForm('profile/index.html.twig', [

        ]);
    }
}

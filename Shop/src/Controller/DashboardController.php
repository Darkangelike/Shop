<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(OrderRepository $orderRepo, UserRepository $userRepo): Response
    {
        $now = new \DateTime();
        $sevenDays = new \DateInterval("P7D");
        $today = new \DateTime();
        $startDate = $now->sub($sevenDays);

//      Nb of orders
        $stats["nbOrders"] = $orderRepo->count([]);

//      Nb total of users in database
        $stats["nbClients"] = $userRepo->count([]);

//      Loop to get all the orders' paid sum
            $total = 0;
        foreach($orderRepo->findAll() as $order){
            $total += $order->getTotal();
        }

//      Total sold
        $stats["totalSold"] = $total;

//      Average sum of each order
        $stats["avgOrder"] = $total / $stats["nbOrders"];

//      Finding all the clients in a date interval
        $newClients = $userRepo->findNewUsersFromTo($startDate, $today);

//      Nb of clients found in the date interval
        $stats["newClients"] = count($newClients);

        return $this->json($stats);
    }


    /**
     *
     * @Route("/test", name="test")
     */
    public function test(CartService $cartService, SessionInterface $session){


        dd($session->getId());

        return $cartService->getSessionId($session);
    }
}

<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order/{id}', name: 'order')]
    public function index(Address $address, CartService $service, EntityManagerInterface $manager): Response
    {
        $order = new Order();

        $order->setUser($this->getUser());
        $order->setCreatedAt(new \DateTime());
        $order->setStatus(1);
        $order->setAddress($address);
        $order->setTotal($service->getTotal());

        foreach ($service->getCart() as $item){
            $orderItem = new OrderItem();

            $orderItem->setCreatedAt(new \DateTime());
            $orderItem->setOrderObject($order);
            $orderItem->setProduct($item["product"]);
            $orderItem->setQuantity($item["quantity"]);

            $manager->persist($orderItem);
        }

        $manager->persist($order);
        $manager->flush();

        $service->emptyCart();

        return $this->redirectToRoute("cart");
    }


    /**
     *
     * @Route("/order/index", name="orders_index")
     * @return Response
     *
     */
    public function orders(){


        return $this->render("order/index.twig.html");
    }
}

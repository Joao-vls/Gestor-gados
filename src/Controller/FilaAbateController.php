<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\FilaAbate;

class FilaAbateController extends AbstractController
{
  /**
  * @Route("/fila/abate", name="app_fila_abate")
  */
  public function index(): Response
  {
    return $this->render('fila_abate/index.html.twig', [
      'controller_name' => 'FilaAbateController',
    ]);
  }
  public function AddFila(Request $request):Response
  {
    $fa = new FilaAbate();
    $gado = $request->query->get('objeto');
    $fa->setMorte(new \DateTime());
    $fa->setInfoantigas($gado);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($fa);
    $entityManager->flush();

    return $this->redirectToRoute('qt-abate');
  }
  public function Filacount(EntityManagerInterface $gd,SessionInterface $session)
  {
    $total = $gd->getRepository(FilaAbate::class)->count([]);
    $session->set('quantidade', $total);
    return $this->redirectToRoute('index');

  }
}

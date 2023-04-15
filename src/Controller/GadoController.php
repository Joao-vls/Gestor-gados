<?php

namespace App\Controller;
use App\Entity\Gado;
use App\Form\GadoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class GadoController extends AbstractController
{
  /**
  * @Route("/gado", name="app_gado")
  */
  public function index(Request $request,SessionInterface $session): Response
  {
    if (!$session->has('quantidade')) {
      return $this->redirectToRoute('qt-abate');
    }
    $gado = new Gado();

    $form = $this->createForm(GadoType::class, $gado);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $gado = $form->getData();
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($gado);
      $entityManager->flush();

      $this->addFlash('success', 'cadastro feito');
      return $this->redirectToRoute('index');
    }

    $gado = $this->getDoctrine()->getRepository(Gado::class)->findAll();
    $melhor_escolha=array();
    $escolha=array();
    $leite_s = 0;
    $racao_s = 0;
    $peso_s = 0;
    $gado1comsumo = 0;
    $hoje = new \DateTime();
    foreach ($gado as $gados) {
      $leite_s += $gados->getLeite();
      $racao_s += $gados->getRacao();
      $peso_s += $gados->getPeso();
      $nasc_s = $gados->getNascimento();
      $idade = $hoje->diff($nasc_s)->y;
      if ($nasc_s->format('md') > $hoje->format('md')) {
        $idade--;
      }
      if ($idade < 1 && $racao_s > 500) {
        $gado1comsumo += 1;
      }
      $gados->getId();
      if($idade>5 && $gados->getLeite()<40 && $gados->getRacao()>50 && $gados->getPeso()>270){
        array_push($melhor_escolha,$gados);
      }else {
        if($idade>5 || $gados->getLeite()<40 || ($gados->getRacao()>50 && $gados->getLeite()<70) || $gados->getPeso()>270){
          array_push($escolha,$gados);
        }
      }
    }

    return $this->render('gado/index.html.twig', [
      'controller_name' => 'GadoController',
      'melhor_escolha' => $melhor_escolha,
      'gados'=>$gado,
      'escolha'=>$escolha,
      'leite_s' => $leite_s,
      'racao_s' => $racao_s,
      'peso_s' => $peso_s,
      'gc_s' => $gado1comsumo,
      'gado_abate'=>$quantidade = $this->get('session')->get('quantidade'),
      'form' => $form->createView()
    ]);

  }
  public function BuscaId($id,SessionInterface $session)
  {
    $Gadomg = $this->getDoctrine()->getManager();
    $gado = $Gadomg->getRepository(Gado::class)->findOneBySomeField($id);
    $resposta = array();
    if (!$gado) {
      $resposta = array('mensagem' => 'Gado com id ' . $id . ' não encontrado');
    } else {
      $session->set('id-b', $id);
      $hoje = new \DateTime();
      $nasc_s = $gado->getNascimento();
      $idade = $hoje->diff($nasc_s)->y;
      if ($nasc_s->format('md') > $hoje->format('md')) {
        $idade--;
      }
      $resposta = [
        'id' => $gado->getId(),
        'leite' => $gado->getPeso(),
        'racao' => $gado->getRacao(),
        'peso' => $gado->getPeso(),
        'idade' => $idade,
      ];
      if($idade>5 || $resposta['leite']<40 || ($resposta['racao']>50 && $resposta['leite']<70) || $resposta['peso']>270){
        $resposta['abate']=true;
      }
    }


    return new Response(json_encode($resposta));
  }
  public function EditarGado( Request $request,SessionInterface $session,$id)
  {
    if($id!=$session->get('id-b')){
      throw $this->createNotFoundException('O gado com ID '.$id.' não foi encontrado.');
    }
    $dados = json_decode($request->getContent(), true);
    $entityManager = $this->getDoctrine()->getManager();
    $gado = $entityManager->getRepository(Gado::class)->find($id);

    if (!$gado) {
      throw $this->createNotFoundException('O gado com ID '.$id.' não foi encontrado.');
    }


    foreach ($dados as $valor) {
      if ($valor['name']=="Leite_ed") {
        $gado->setLeite($valor['valor']);
      }
      if ($valor['name']=="Peso_ed") {
        $gado->setPeso($valor['valor']);
      }
      if ($valor['name']=="Racao_ed") {
        $gado->setRacao($valor['valor']);
      }
      if ($valor['name']=="Date_ed") {
        $hoje = new \DateTime();
        $formato = 'Y-m-d';
        $data = \DateTime::createFromFormat($formato, $valor['valor']);
        $idade = $hoje->diff($data)->y;
        if ($data->format('md') > $hoje->format('md')) {
          $idade--;
        }
        if($idade<0){
          throw $this->createNotFoundException('Data invalida');

        }
        $gado->setNascimento($data);
      }

    }
    $entityManager->flush();
    return new Response(json_encode($dados));

  }
  public function DeletarGado($id,SessionInterface $session)
  {
    if($id!=$session->get('id-b')){
      throw $this->createNotFoundException('O gado com ID '.$id.' não foi encontrado.');
    }
    $entityManager = $this->getDoctrine()->getManager();
    $gado = $entityManager->getRepository(Gado::class)->find($id);

    if (!$gado) {
      throw $this->createNotFoundException('id invalido ');
    }

    $entityManager->remove($gado);
    $entityManager->flush();

    return $this->redirectToRoute('index');
  }
  public function MandarFilaAbate($id,SessionInterface $session,SerializerInterface $seria)
  {
    if($id!=$session->get('id-b')){
      throw $this->createNotFoundException('O gado com ID '.$id.' não foi encontrado.');
    }
    $entityManager = $this->getDoctrine()->getManager();
    $gado = $entityManager->getRepository(Gado::class)->find($id);
    if (!$gado) {
      throw $this->createNotFoundException('id invalido ');
    }
    $entityManager->remove($gado);
    $entityManager->flush();
    $resposta = [
      'id' => $gado->getId(),
      'leite' => $gado->getPeso(),
      'racao' => $gado->getRacao(),
      'peso' => $gado->getPeso(),
    ];
    $gado=json_encode($resposta);
    return $this->redirectToRoute('id-abate', ['objeto' => $gado]);
  }
}

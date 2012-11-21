<?php

namespace MD\DebateBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MD\DebateBundle\Entity\Debate;
use MD\DebateBundle\Entity\Contention;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DebateController extends Controller
{
  public function indexAction()
  {    
    return $this->render('MDDebateBundle:Default:index.html.twig', array('name'=>'Craig'));
  }
  public function debateCreateAction(Request $request)
  {
    // create a task and give it some dummy data for this example
    $debate = new Debate();
    $debate->setName('');

    $form = $this->createForm(new \MD\DebateBundle\Form\DebateType(), $debate);

    if ($request->isMethod('POST')) {
        $debate->setCreated(new \DateTime('now'));

        $form->bind($request);
        if ($form->isValid()) {
            // perform some action, such as saving the task to the database
            $this->get('session')->setFlash('notice', 'New Debate Created.');
            $em = $this->getDoctrine()->getManager();
            $em->persist($debate);
            $em->flush();
            $id = $debate->getId();
            return $this->redirect($this->generateUrl('md_debate_list', array('id'=>$id)));
        }
    }

    return $this->render('MDDebateBundle:Debate:form_debate.html.twig', array(
        'form' => $form->createView(),
    ));
    /*
    $debate = new Debate();
    $debate->setName('A Fool Bear');
    $debate->setDescription('FLorem Flipsum Flodolor');

    $em = $this->getDoctrine()->getManager();
    $em->persist($debate);
    $em->flush();

    return new Response('Created debate id '.$debate->getId()); */
  }
  public function debateListAction($id)
  {
      $debate = $this->getDoctrine()
          ->getRepository('MDDebateBundle:Debate')
          ->find($id);
      foreach ($debate->getContentions() as $contention) {
          if ($contention->getAff()) {
              $contentions['aff'][] = $contention;
          }
          else {
              $contentions['neg'][] = $contention;
          }
      }

      if (!$debate) {
          throw $this->createNotFoundException('No debate found for that id ('.$id.')');
      }
      return $this->render('MDDebateBundle:Debate:view_debate.html.twig', array(
          'debate' => $debate,
          'id'     => $id,
          'contentions' => $contentions,
      ));
  }

  public function contentionCreateAction(Request $request, $id, $aff = 'x')
  {
      $debate = new Debate();
      $debate = $this->getDoctrine()
          ->getRepository('MDDebateBundle:Debate')
          ->find($id);

      $contention = new Contention();

      // Process for affirmative
      if ($aff == 'aff') {
          $contention->setAff(true);
      }
      if ($aff == 'neg') {
          $contention->setAff(false);
      }
      $form = $this->createForm(new \MD\DebateBundle\Form\ContentionType(), $contention);

      if ($request->isMethod('POST')) {
          $contention->setCreated(new \DateTime('now'));
          $contention->setDebate($debate);

          $form->bind($request);
          if ($form->isValid()) {
             // perform some action, such as saving the task to the database
              $this->get('session')->setFlash('notice', 'Your changes were saved!');
              $em = $this->getDoctrine()->getManager();
              $em->persist($debate);
              $em->persist($contention);
              $em->flush();
              return $this->redirect($this->generateUrl('md_debate_list', array('id'=>$id)));
          }
      }

      return $this->render('MDDebateBundle:Debate:form_contention.html.twig', array(
          'form' => $form->createView(),
          'id' => $id,
      ));
  }
}

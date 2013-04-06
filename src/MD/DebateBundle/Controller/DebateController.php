<?php

namespace MD\DebateBundle\Controller;

use MD\DebateBundle\Entity\ContentionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MD\DebateBundle\Entity\Debate;
use MD\DebateBundle\Entity\Contention;
use MD\DebateBundle\Entity\Point;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class DebateController extends Controller
{
    public function indexAction()
    {
    return $this->render('MDDebateBundle:Default:index.html.twig');
    }

    /* Display a Debate */
    public function templateViewAction($type) {
        switch ($type) {
            default:
            case 'debate':
                return $this->render('MDDebateBundle:Debate:template_debate.html.php');
                break;
            case 'contention':
                // Contention Template
                break;
            case 'point':
                // Point Template
                break;
        }
    }

    /* Disambiguate Debate requests via RESTful API */
    public function debateRestAction($id)
    {
        $request = Request::createFromGlobals();
        switch ($request->getMethod()) {
            default:
            case 'GET':
                return $this->debateRestGet($id);
                break;
            case 'POST':
            case 'PUT':
                return $this->debateRestWrite($request, $id);
                break;
        }
    }
    protected function debateRestGet($id) {
        if ($id == 'all') {
            // @todo load all debates
            return new Response('<html><body>Load all debates here.</body></html>');
        }
        else {
            $fullDebate = $this->debateLoader($id, true);
            $fullDebate->sortContentions();
            // Assert access control
            $fullDebate->setEditable( $this->checkEditable($fullDebate) );
            $serializer = $this->get('serializer');
            $data = $serializer->serialize($fullDebate, 'json'); // json|xml|yml
            return new Response($data);
        }
    }
    protected function debateRestWrite(Request $request, $did) {
        $method = $request->getMethod();

        // Get transmitted data
        $serializer = $this->get('serializer');
        $newDebate = $serializer->deserialize($request->getContent(), 'MD\DebateBundle\Entity\Debate', 'json');

        if ($method == 'PUT') {
            if (!($did = $newDebate->getId())) {
                throw new NotFoundHttpException("Cannot update Debate without Debate ID");
            }
            else {
                // Load Debate
                $debate = $this->debateLoader($did);
                // Update
                $debate->updateDebate($newDebate, true);
                // Save
                $this->getDoctrine()->getManager()->flush();

                // Prepare to view
                $debate = $debate->basicCopy();
                $debate->setEditable( $this->checkEditable($debate) );

                // Serialize results
                $serializer = $this->get('serializer');
                $data = $serializer->serialize($debate, 'json'); // json|xml|yml
                return new Response($data);
            }
        }
        elseif ($method == 'POST') {
            $debate = new Debate();
            $debate->setCreated(new \DateTime('now'));

            $form->bind($newDebate);
            if ($form->isValid()) {
                // perform some action, such as saving the task to the database
                $this->get('session')->setFlash('notice', 'New Debate Created.');
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                // Retrieving the security identity of the currently logged-in user
                $securityContext = $this->get('security.context');
                $user = $securityContext->getToken()->getUser();
                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                // Setting up the objectIdentity
                $aclProvider = $this->get('security.acl.provider');
                $objectIdentity = ObjectIdentity::fromDomainObject($newDebate);
                try {
                    // If there is no ACL for the company yet, an
                    // AclNotFoundException is thrown
                    $acl = $aclProvider->findAcl(
                        $objectIdentity,
                        array($securityIdentity)
                    );

                    // For some reason we need to use an index to update an
                    // ACE, so we need to use an index, so start looping
                    foreach($acl->getObjectAces() as $index => $ace) {
                        $aceSecurityId = $ace->getSecurityIdentity();
                        if($aceSecurityId ->equals($securityIdentity)) {
                            $acl->updateObjectAce(
                                $index,
                                MaskBuilder::MASK_OWNER
                            );
                        }
                    }
                    $aclProvider->updateAcl($acl);
                } catch (AclNotFoundException $e) {
                    // No existing ACL found so create a new one
                    $acl = $aclProvider->createAcl($objectIdentity);
                    $acl->insertObjectAce(
                        $securityIdentity,
                        MaskBuilder::MASK_OWNER
                    );
                    $aclProvider->updateAcl($acl);
                }
                $newDebate->setEditable( true );
                $serializer = $this->get('serializer');
                $data = $serializer->serialize($newDebate, 'json'); // json|xml|yml
                return new Response($data);
            }
            else {
                print '!!errors!!';
                print_r($form->getErrors());
                $data = json_encode($form->getErrors());
                return new Response($data);
            }
        }

        $form = $this->createForm(new \MD\DebateBundle\Form\DebateType(), $debate);
    }

    /**
     * Load a debate
     * @did - The Debate ID
     * @checkAccess - Boolean indicating whether we need to check access before loading this Debate
     *
     * @return = the debate, if loaded and accessible, or false if inaccessible
     */
    protected function debateLoader($did, $checkAccess = true) {
        $em = $this->getDoctrine()->getManager();
        // We're showing an individual response
        $fullDebate = $em->getRepository('MDDebateBundle:Debate')
            ->find($did);
        if (!($checkAccess) || $this->checkEditable($fullDebate)) {
            return $fullDebate;
        }
        else {
            return false;
        }
    }
    /**
     * Load a contention
     * @cid - The Contention ID
     * @checkAccess - Boolean indicating whether we need to check access before loading this Debate
     *
     * @return = the debate, if loaded and accessible, or false if inaccessible
     */
    protected function contentionLoader($cid, $checkAccess = true) {
        $em = $this->getDoctrine()->getManager();
        // We're showing an individual response
        $contention = $em->getRepository('MDDebateBundle:Contention')
            ->find($cid);
        if (!($checkAccess) || $this->checkEditable($contention)) {
            return $contention;
        }
        else {
            return false;
        }
    }

    /**
     * Contention REST disambiguator
     */
    /* Disambiguate Debate requests via RESTful API */
    public function contentionRestAction($did, $cid)
    {
        $request = Request::createFromGlobals();
        $request->getMethod();

        switch ($request->getMethod()) {
            default:
            case 'GET':
                return $this->contentionRestGet($did, $cid);
                break;
            case 'POST':
            case 'PUT':
                return $this->contentionRestWrite($request, $did, $cid);
                break;
        }
    }
    protected function contentionRestGet($did, $cid) {
        if ($cid == 'all') {
            // @todo display all contentions
            return new Response('<html><body>List all contentions here.</body></html>');
        }
        else {
            // @todo display a single contention
            return new Response('<html><body>Show contention '.$cid.' here.</body></html>');
        }
    }
    protected function contentionRestWrite(Request $request, $did, $cid = null) {
        $method = $request->getMethod();

        // Get transmitted data
        $serializer = $this->get('serializer');
        $newContention = $serializer->deserialize($request->getContent(), 'MD\DebateBundle\Entity\Contention', 'json');

        if (!($newContention->getAff())) {
            $newContention->setAff('neg');
        }

        $debate = $this->debateLoader($did);

        if ($method == 'PUT') {
            // Load debate, thereby checking access
            $debate = $this->debateLoader($did);

            // Build the contention
            $contention = $this->contentionLoader($cid);
            $contention->updateContention($newContention);

            // Save the contention
            $this->getDoctrine()->getManager()->flush();

            // Serialize results
            $serializer = $this->get('serializer');
            $data = $serializer->serialize($contention, 'json'); // json|xml|yml
            return new Response($data);
        }
        elseif ($method == 'POST') {
            $contention = new Contention();
            $contention->setCreated(new \DateTime('now'));
            $contention->setDebate($debate);

            // Update
            $contention->updateContention($newContention);
            // Save
            $this->getDoctrine()->getManager()->persist($contention);
            $this->getDoctrine()->getManager()->flush();

            // Serialize results
            $serializer = $this->get('serializer');
            $data = $serializer->serialize($contention, 'json'); // json|xml|yml
            return new Response($data);
        }
    }

    /**
     *  Return a Debate form
     *  $did is the id of Debate Entity
     */
    public function debateFormAction($did)
    {
        if (isset($did) && is_numeric($did)) {
            // We're editing a debate, not creating a new one.
            $debate = $this->debateLoader($did, true);
        }
        else {
            $debate = new Debate();
        }

        $form = $this->createForm(new \MD\DebateBundle\Form\DebateType(), $debate);
        return $this->render('MDDebateBundle:Debate:form_debate.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     *  Return Contention form
     *  $did is the id of Debate Entity
     *  $cid is the id of Contention Entity, if creating a new one
     *  $aff is a boolean representing whether a new contention is affirmative or not
     */
    public function contentionFormAction($did, $cid, $aff)
    {
        $contention = new Contention();
        if ($did == 'new') {
            // THROW EXCEPTION HERE
            // WE NEED A DEBATE ID!
            return;
        }
        else {
            if ($cid == 'new') {
                // New contention Form
                // First load the debate this contention is being added to
                $debate = $this->debateLoader($did, true);

                $contention = new Contention();
                $contention->setDebate($debate);

                // Convert string "aff" or "neg" to booleans answering "Is this contention affirmative?"
                if ($aff === 'aff') {
                    $aff = true;
                }
                elseif ($aff === 'neg') {
                    $aff = false;
                }

                $options = array();
                $form = $this->createForm(new \MD\DebateBundle\Form\ContentionType(), $contention, $options);
                return $this->render('MDDebateBundle:Contention:form_contention.html.twig', array(
                    'form' => $form->createView(),
                    'did'  => $did,
                    'aff'  => $aff
                ));
            }
            else {
                // Editing a contention
                $contention = $this->contentionLoader($cid, true);

                $options = array();
                $form = $this->createForm(new \MD\DebateBundle\Form\ContentionType(), $contention, $options);
                return $this->render('MDDebateBundle:Contention:form_contention.html.twig', array(
                    'form' => $form->createView(),
                    'did'  => $did,
                    'aff'  => $contention->getAff()
                ));
            }
        }
    }


    public function contentionCreateAction(Request $request, $id, $aff = 'all')
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

    }

    public function contentionPointListAction($cid, $pid)
    {
        $contention = new Contention();
        $contention = $this->getDoctrine()
            ->getRepository('MDDebateBundle:Contention')
            ->find($cid);
        $points = $contention->getPoints();

        return $this->render('MDDebateBundle:Point:pointItems.html.twig', array(
            'points' => $points
        ));
    }

    public function pointCreateAction(Request $request, $did, $cid)
    {
        $contention = new Contention();
        $contention = $this->getDoctrine()
            ->getRepository('MDDebateBundle:Contention')
            ->find($cid);
        /* @todo: check contention access */

        $point = new Point();
        $point->setContention($contention);

        $form = $this->createForm(new \MD\DebateBundle\Form\PointType(), $point);

        if ($request->isMethod('POST')) {
            $time = new \DateTime('now');
            // Set the "created" time to now only if it's new.
            if ($point->getCreated() == '') {
              $point->setCreated($time);
            }
            $point->setEdited($time);

            $form->bind($request);
            if ($form->isValid()) {
                // perform some action, such as saving the task to the database
                $this->get('session')->setFlash('notice', 'Your point was saved!');
                $em = $this->getDoctrine()->getManager();
                $em->persist($contention);
                $em->persist($point);
                $em->flush();
                return $this->redirect($this->generateUrl('md_debate_list', array('id'=>$did)));
            }
        }

        return $this->render('MDDebateBundle:Point:form_point.html.twig', array(
            'form' => $form->createView(),
            'did' => $did,
            'cid' => $cid,
        ));
    }

    /* See if the user has access to edit an Object */
    private function checkEditable($obj)
    {
        $securityContext = $this->get('security.context');

        $grant = false;
        if (true === $securityContext->isGranted('ROLE_ADMIN')) {
            $grant = true;
        }
        if (true === $securityContext->isGranted('EDIT', $obj)) {
            $grant = true;
        }
        return $grant;
    }
}

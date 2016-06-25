<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\UserMap;
use AppBundle\Form\UserMapType;

/**
 * UserMap controller.
 *
 * @Route("/usermap")
 * @Security("has_role('ROLE_USER')")
 */
class UserMapController extends Controller
{
    /**
     * Lists all UserMap entities.
     *
     * @Route("/", name="usermap_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $userMaps = $em->getRepository('AppBundle:UserMap')->findAll();

        return $this->render('usermap/index.html.twig', array(
                'userMaps' => $userMaps,
        ));
    }

    /**
     * Creates a new UserMap entity.
     *
     * @Route("/new", name="usermap_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $userMap = new UserMap();
        $form = $this->createForm('AppBundle\Form\UserMapType', $userMap);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userMap->setAuthor($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($userMap);
            $em->flush();

            return $this->redirectToRoute('usermap_show', array('id' => $userMap->getId()));
        }

        return $this->render('usermap/new.html.twig', array(
                'userMap' => $userMap,
                'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UserMap entity.
     *
     * @Route("/{id}", name="usermap_show")
     * @Method("GET")
     */
    public function showAction(UserMap $userMap)
    {
        $deleteForm = $this->createDeleteForm($userMap);

        return $this->render('usermap/show.html.twig', array(
                'userMap' => $userMap,
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing UserMap entity.
     *
     * @Route("/{id}/edit", name="usermap_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('edit', userMap)")
     */
    public function editAction(Request $request, UserMap $userMap)
    {
        $deleteForm = $this->createDeleteForm($userMap);
        $editForm = $this->createForm('AppBundle\Form\UserMapType', $userMap);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($userMap);
            $em->flush();

            return $this->redirectToRoute('usermap_edit', array('id' => $userMap->getId()));
        }

        return $this->render('usermap/edit.html.twig', array(
                'userMap' => $userMap,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a UserMap entity.
     *
     * @Route("/{id}", name="usermap_delete")
     * @Method("DELETE")
     * @Security("is_granted('edit', post)")
     */
    public function deleteAction(Request $request, UserMap $userMap)
    {
        $form = $this->createDeleteForm($userMap);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userMap->setArchived(true);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return $this->redirectToRoute('usermap_index');
    }

    /**
     * Creates a form to delete a UserMap entity.
     *
     * @param UserMap $userMap The UserMap entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(UserMap $userMap)
    {
        return $this->createFormBuilder()
                ->setAction($this->generateUrl('usermap_delete', array('id' => $userMap->getId())))
                ->setMethod('DELETE')
                ->getForm();
    }
}

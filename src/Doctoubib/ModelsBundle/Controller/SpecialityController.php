<?php

namespace Doctoubib\ModelsBundle\Controller;

use Doctoubib\ModelsBundle\Entity\Speciality;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Speciality controller.
 *
 * @Route("speciality")
 */
class SpecialityController extends Controller
{
    /**
     * Lists all speciality entities.
     *
     * @Route("/", name="speciality_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $specialities = $em->getRepository('DoctoubibModelsBundle:Speciality')->findAll();

        return $this->render('speciality/index.html.twig', array(
            'specialities' => $specialities,
        ));
    }

    /**
     * Creates a new speciality entity.
     *
     * @Route("/new", name="speciality_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $speciality = new Speciality();
        $form = $this->createForm('Doctoubib\ModelsBundle\Form\SpecialityType', $speciality);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($speciality);
            $em->flush($speciality);

            return $this->redirectToRoute('speciality_show', array('id' => $speciality->getId()));
        }

        return $this->render('speciality/new.html.twig', array(
            'speciality' => $speciality,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a speciality entity.
     *
     * @Route("/{id}", name="speciality_show")
     * @Method("GET")
     */
    public function showAction(Speciality $speciality)
    {
        $deleteForm = $this->createDeleteForm($speciality);

        return $this->render('speciality/show.html.twig', array(
            'speciality' => $speciality,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing speciality entity.
     *
     * @Route("/{id}/edit", name="speciality_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Speciality $speciality)
    {
        $deleteForm = $this->createDeleteForm($speciality);
        $editForm = $this->createForm('Doctoubib\ModelsBundle\Form\SpecialityType', $speciality);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('speciality_edit', array('id' => $speciality->getId()));
        }

        return $this->render('speciality/edit.html.twig', array(
            'speciality' => $speciality,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a speciality entity.
     *
     * @Route("/{id}", name="speciality_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Speciality $speciality)
    {
        $form = $this->createDeleteForm($speciality);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($speciality);
            $em->flush($speciality);
        }

        return $this->redirectToRoute('speciality_index');
    }

    /**
     * Creates a form to delete a speciality entity.
     *
     * @param Speciality $speciality The speciality entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Speciality $speciality)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('speciality_delete', array('id' => $speciality->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

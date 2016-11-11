<?php

namespace Doctoubib\ModelsBundle\Controller;

use Doctoubib\ModelsBundle\Entity\Insurance;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Insurance controller.
 *
 * @Route("insurance")
 */
class InsuranceController extends Controller
{
    /**
     * Lists all insurance entities.
     *
     * @Route("/", name="insurance_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $insurances = $em->getRepository('DoctoubibModelsBundle:Insurance')->findAll();

        return $this->render('insurance/index.html.twig', array(
            'insurances' => $insurances,
        ));
    }

    /**
     * Creates a new insurance entity.
     *
     * @Route("/new", name="insurance_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $insurance = new Insurance();
        $form = $this->createForm('Doctoubib\ModelsBundle\Form\InsuranceType', $insurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($insurance);
            $em->flush($insurance);

            return $this->redirectToRoute('insurance_show', array('id' => $insurance->getId()));
        }

        return $this->render('insurance/new.html.twig', array(
            'insurance' => $insurance,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a insurance entity.
     *
     * @Route("/{id}", name="insurance_show")
     * @Method("GET")
     */
    public function showAction(Insurance $insurance)
    {
        $deleteForm = $this->createDeleteForm($insurance);

        return $this->render('insurance/show.html.twig', array(
            'insurance' => $insurance,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing insurance entity.
     *
     * @Route("/{id}/edit", name="insurance_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Insurance $insurance)
    {
        $deleteForm = $this->createDeleteForm($insurance);
        $editForm = $this->createForm('Doctoubib\ModelsBundle\Form\InsuranceType', $insurance);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('insurance_edit', array('id' => $insurance->getId()));
        }

        return $this->render('insurance/edit.html.twig', array(
            'insurance' => $insurance,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a insurance entity.
     *
     * @Route("/{id}", name="insurance_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Insurance $insurance)
    {
        $form = $this->createDeleteForm($insurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($insurance);
            $em->flush($insurance);
        }

        return $this->redirectToRoute('insurance_index');
    }

    /**
     * Creates a form to delete a insurance entity.
     *
     * @param Insurance $insurance The insurance entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Insurance $insurance)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('insurance_delete', array('id' => $insurance->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

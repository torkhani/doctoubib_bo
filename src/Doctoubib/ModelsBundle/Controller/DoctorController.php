<?php

namespace Doctoubib\ModelsBundle\Controller;

use Doctoubib\ModelsBundle\Entity\Doctor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Doctor controller.
 *
 * @Route("doctor")
 */
class DoctorController extends Controller
{
    /**
     * Lists all doctor entities.
     *
     * @Route("/", name="doctor_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $doctors = $em->getRepository('DoctoubibModelsBundle:Doctor')->findAll();

        return $this->render('doctor/index.html.twig', array(
            'doctors' => $doctors,
        ));
    }

    /**
     * Creates a new doctor entity.
     *
     * @Route("/new", name="doctor_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $doctor = new Doctor();
        $form = $this->createForm('Doctoubib\ModelsBundle\Form\DoctorType', $doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($doctor);
            $em->flush($doctor);

            return $this->redirectToRoute('doctor_show', array('id' => $doctor->getId()));
        }

        return $this->render('doctor/new.html.twig', array(
            'doctor' => $doctor,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a doctor entity.
     *
     * @Route("/{id}", name="doctor_show")
     * @Method("GET")
     */
    public function showAction(Doctor $doctor)
    {
        $deleteForm = $this->createDeleteForm($doctor);

        return $this->render('doctor/show.html.twig', array(
            'doctor' => $doctor,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing doctor entity.
     *
     * @Route("/{id}/edit", name="doctor_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Doctor $doctor)
    {
        $deleteForm = $this->createDeleteForm($doctor);
        $editForm = $this->createForm('Doctoubib\ModelsBundle\Form\DoctorType', $doctor);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('doctor_edit', array('id' => $doctor->getId()));
        }

        return $this->render('doctor/edit.html.twig', array(
            'doctor' => $doctor,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a doctor entity.
     *
     * @Route("/{id}", name="doctor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Doctor $doctor)
    {
        $form = $this->createDeleteForm($doctor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($doctor);
            $em->flush($doctor);
        }

        return $this->redirectToRoute('doctor_index');
    }

    /**
     * Creates a form to delete a doctor entity.
     *
     * @param Doctor $doctor The doctor entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Doctor $doctor)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('doctor_delete', array('id' => $doctor->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}

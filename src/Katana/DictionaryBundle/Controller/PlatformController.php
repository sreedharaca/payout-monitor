<?php

namespace Katana\DictionaryBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Katana\DictionaryBundle\Entity\Platform;
use Katana\DictionaryBundle\Form\PlatformType;

/**
 * Platform controller.
 *
 * @Route("/platform")
 */
class PlatformController extends Controller
{

    /**
     * Lists all Platform entities.
     *
     * @Route("/", name="platform")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KatanaDictionaryBundle:Platform')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Platform entity.
     *
     * @Route("/", name="platform_create")
     * @Method("POST")
     * @Template("KatanaDictionaryBundle:Platform:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Platform();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('platform_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Platform entity.
    *
    * @param Platform $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Platform $entity)
    {
        $form = $this->createForm(new PlatformType(), $entity, array(
            'action' => $this->generateUrl('platform_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Platform entity.
     *
     * @Route("/new", name="platform_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Platform();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Platform entity.
     *
     * @Route("/{id}", name="platform_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KatanaDictionaryBundle:Platform')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Platform entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Platform entity.
     *
     * @Route("/{id}/edit", name="platform_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KatanaDictionaryBundle:Platform')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Platform entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Platform entity.
    *
    * @param Platform $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Platform $entity)
    {
        $form = $this->createForm(new PlatformType(), $entity, array(
            'action' => $this->generateUrl('platform_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Platform entity.
     *
     * @Route("/{id}", name="platform_update")
     * @Method("PUT")
     * @Template("KatanaDictionaryBundle:Platform:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KatanaDictionaryBundle:Platform')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Platform entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('platform_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Platform entity.
     *
     * @Route("/{id}", name="platform_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('KatanaDictionaryBundle:Platform')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Platform entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('platform'));
    }

    /**
     * Creates a form to delete a Platform entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('platform_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}

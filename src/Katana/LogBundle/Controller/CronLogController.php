<?php

namespace Katana\LogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Katana\LogBundle\Entity\CronLog;

/**
 * CronLog controller.
 *
 * @Route("/cronlog")
 */
class CronLogController extends Controller
{

    /**
     * Lists all CronLog entities.
     *
     * @Route("/", name="cronlog")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KatanaLogBundle:CronLog')->findBy(array(), array('created'=>'DESC'));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Finds and displays a CronLog entity.
     *
     * @Route("/{id}", name="cronlog_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KatanaLogBundle:CronLog')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CronLog entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }
}

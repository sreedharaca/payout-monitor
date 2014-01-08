<?php

namespace Katana\LogBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Katana\LogBundle\Entity\Log;

/**
 * Log controller.
 *
 * @Route("/log")
 */
class LogController extends Controller
{

    /**
     * Lists all Log entities.
     *
     * @Route("/", name="log")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('KatanaLogBundle:Log')->findAllLogs();

        $actionToCss = array(
            Log::ACTION_NEW => 'label-success',
            Log::ACTION_PAYOUT_CHANGE => 'label-warning',
            Log::ACTION_STOP => 'label-important'
        );

        return array(
            'entities' => $entities,
            'actionToCss' => $actionToCss
        );
    }

    /**
     * Finds and displays a Log entity.
     *
     * @Route("/{id}", name="log_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('KatanaLogBundle:Log')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Log entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }
}

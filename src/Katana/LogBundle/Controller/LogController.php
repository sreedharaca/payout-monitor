<?php

namespace Katana\LogBundle\Controller;


use Katana\LogBundle\Form\FormType;
use Katana\LogBundle\Model\FormFilter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Katana\LogBundle\Entity\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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

        $paginator = $em->getRepository('KatanaLogBundle:Log')->findLogsPaging();

        $typeCssClass = array(
            Log::ACTION_NEW => 'label-success',
            Log::ACTION_PAYOUT_CHANGE => 'label-warning',
            Log::ACTION_STOP => 'label-important'
        );

        $eventTypes = array(Log::ACTION_NEW, Log::ACTION_PAYOUT_CHANGE, Log::ACTION_STOP);

        return array(
            'form'       => $this->createForm(new FormType())->createView(),
            'total'      => count($paginator),
            'entities'   => $paginator->getIterator(),
            'eventTypes' => $eventTypes
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


    /**
     *
     *
     * @Route("/ajax_filter", name="log_ajax_filter")
     * @Method("POST")
     */
    public function getAjaxEvents(Request $request)
    {
        $FormFilter = new FormFilter();
        $FormFilter->bind($request);

        $formData = $FormFilter->getData();

        $em = $this->getDoctrine()->getManager();

        $paginator = $em->getRepository('KatanaLogBundle:Log')->findLogsByFilter($formData, $formData['offset']);


        $typeCssClass = array(
            Log::ACTION_NEW => 'label-success',
            Log::ACTION_PAYOUT_CHANGE => 'label-warning',
            Log::ACTION_STOP => 'label-danger'
        );


        $DateToTextService = $this->container->get('DateToTextService');

        $events = array();

        foreach($paginator->getIterator() as $event){

            $countries = $event->getOffer()->getCountries();
            $c_codes = array();

            foreach($countries as $country){
                $c_codes[] = $country->getCode();
            }


            $event_data = array(
                'id'      => $event->getId(),
                'type'    => $event->getAction(), //todo rename action --> type
                'typeCssClass' => $typeCssClass[$event->getAction()],
                'message' => $event->getMessage(),
                'time'    => $DateToTextService->dateToText($event->getCreated()),
                'offer'   =>  array(
                    'name'       => $event->getOffer()->getName(),
                    'payout'     => $event->getOffer()->getPayout(),
                    'previewUrl' => $event->getOffer()->getPreviewUrl(),
                    'affiliate'  =>  array(
                        'name'   => $event->getOffer()->getAffiliate()->getName()
                    ),
                    'platform'   => array(
                        'name'   => ($event->getOffer()->getPlatform() ? $event->getOffer()->getPlatform()->getName() : '')
                    ),
                    'country'    => $c_codes
                )
            );

            $events[] = $event_data;
        }

        return new JsonResponse(
            array(
                'success'    => true,
                'events'     => $events,
                'totalCount' => count($paginator)
            )
        );
    }
}

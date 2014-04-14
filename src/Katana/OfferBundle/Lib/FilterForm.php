<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 17.01.14
 * Time: 15:39
 */

namespace Katana\OfferBundle\Lib;


use Symfony\Component\HttpFoundation\Request;



class FilterForm {

    private $data;

    public function getData(){
        return $this->data;
    }

    public function bind(Request $request){

        if ($request->getMethod() == 'POST'){

            $this->data['search']     = $request->request->get('searchText');
            $this->data['affiliate']  = $request->request->get('affiliate');
            $this->data['platform']   = $request->request->get('platform');
            $this->data['country']    = $request->request->get('country');
            $this->data['device']     = $request->request->get('device');
            $this->data['new']        = filter_var($request->request->get('new'), FILTER_VALIDATE_BOOLEAN);
            $this->data['incentive']  = filter_var($request->request->get('incentive'), FILTER_VALIDATE_BOOLEAN);
            $this->data['offset']     = intval($request->request->get('offset'));
            $this->data['sort_column']= $request->request->get('sort_column');
            $this->data['sort_asc']   = filter_var($request->request->get('sort_asc'), FILTER_VALIDATE_BOOLEAN);
            $this->data['offer_id']   = intval($request->request->get('offer_id'));
        }
        else{

            $this->data['search']     = null;
            $this->data['affiliate']  = null;
            $this->data['platform']   = null;
            $this->data['country']    = array();
            $this->data['device']     = null;
            $this->data['new']        = null;
            $this->data['incentive']  = null;
            $this->data['offset']     = 0;
            $this->data['sort_column']= null;
            $this->data['sort_asc']   = null;
            $this->data['offer_id']   = null;
//            throw new \Exception('Другие методы запросов не реализованы');
        }
    }

} 
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
        }
        else{
            throw new \Exception('Другие методы запросов не реализованы');
        }
    }

} 
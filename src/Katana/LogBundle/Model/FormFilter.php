<?php
/**
 * Created by PhpStorm.
 * User: Aslan
 * Date: 22.01.14
 * Time: 20:48
 */

namespace Katana\LogBundle\Model;


use Symfony\Component\HttpFoundation\Request;



class FormFilter {

    private $data = array();

    public function bind(Request $request){

        $this->data['offset'] = intval($request->request->get('offset'));

        //types checkboxes
        $this->data['type'] = array();
        foreach($request->request->get('types') as $type){
            if ($type['value'] == 1) {
                $this->data['type'][] = $type['name'];
            }
        }

        //countries
        $country = $request->request->get('country');
        if(!empty( $country )){
            $this->data['country'] = array();
            foreach($request->request->get('country') as $country){
                $this->data['country'][] = intval($country);
            }
        }
    }

    public function getData(){
        return $this->data;
    }

} 
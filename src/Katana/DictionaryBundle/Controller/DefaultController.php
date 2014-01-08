<?php

namespace Katana\DictionaryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('KatanaDictionaryBundle:Default:index.html.twig', array('name' => $name));
    }
}

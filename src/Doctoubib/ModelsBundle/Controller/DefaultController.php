<?php

namespace Doctoubib\ModelsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DoctoubibModelsBundle:Default:index.html.twig');
    }
}

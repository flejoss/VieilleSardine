<?php

namespace VieilleSardine\CatalogueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CatalogueController extends Controller
{
    public function indexAction()
    {
        return $this->render('VieilleSardineCatalogueBundle:Catalogue:index.html.twig');
    }
}

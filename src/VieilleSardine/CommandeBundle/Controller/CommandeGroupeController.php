<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace VieilleSardine\CommandeBundle\Controller;

use VieilleSardine\CommandeBundle\Entity\Lignes;
use VieilleSardine\UserBundle\Entity\Client;
use VieilleSardine\UserBundle\Entity\CompteClient;
use VieilleSardine\ProduitBundle\Entity\Produit;
use VieilleSardine\CommandeBundle\Entity\CommandeGroupee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommandeGroupeController extends Controller
{
     public function indexAction()
    {
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeGroupeVPC.html.twig');
    }
    // Méthode qui créer un tableau avec toutes les infos de la commande groupée
    public function CreerFormRecapCommandeGroupeAction()
    {
        $commandeGroupe = $this->getDoctrine()
        ->getRepository('CommandeBundle:CommandeGroupee')
        ->find($id);

    if (!$produit) {
        throw $this->createNotFoundException(
            'Aucun produit trouvé pour cet id : '.$id
        );
    }
    }
    
    public function formNewClientAction(Request $request)
    {
        $client = new Client();
        $compteclient = new CompteClient();
        /*$client->setID('id du client');;
        */
        $form = $this->createFormBuilder($client)
            ->add('nom', 'text')
            ->add('prenom', 'text')
            ->add('mail', 'text')
            ->add('codePostal', 'text')
            ->add('ville', 'text')
            ->getForm();

        return $this->render('AcmeTaskBundle:Default:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /*
    // Méthode pour retrouver les attributs d'un produit
    public function getInfoProduitAction($id)
    {
    $produit = $this->getDoctrine()
        ->getRepository('ProduitBundle:Produit')
        ->find($id);

    if (!$produit) {
        throw $this->createNotFoundException(
            'Aucun produit trouvé pour cet id : '.$id
        );
    }

    
}*/
}

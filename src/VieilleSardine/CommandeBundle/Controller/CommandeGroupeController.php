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
         $session = $request->getSession();

        // stocke un attribut pour une réutilisation lors d'une future requête utilisateur
      //  $session->set('foo', 'bar');

        // dans un autre contrôleur pour une autre requête
        $idCmdG = $session->get('idCommande');
        $tab[] = CreerFormRecapCommandeGroupeAction($idCmdG);
        
        $idClt = $session->get('idClient');
        $form = formClientAction($idClt);
        // utilise une valeur par défaut si la clé n'existe pas
      //  $filters = $session->get('filters', array());
        
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeGroupeVPC.html.twig', array('form' => 
            $form->createView()), array("tab" => $tab[])
                ); 
    }
    // Méthode qui créer un tableau avec toutes les infos de la commande groupée
    public function CreerFormRecapCommandeGroupeAction($idCmdSimple)
    {
        $listeCmdSimples = $this->getDoctrine()
        ->getRepository('CommandeBundle:Commande_Groupee')
        ->find($idCmdSimple);

        if (!listeCmdSimple) {
            throw $this->createNotFoundException(
                'Aucune commande trouvée pour cet id : '.$id
            );
        }
 
        $ligne[] = $this->getDoctrine()
                ->getRepository('CommandeBundle:Lignes')
                ->find(listeCmdSimple);
   //         $listeCmdSimples->find("VehicleCatalogue\Model\Car", array("name" => "Audi A8", "year" => 2010));
        return $this->render( array("ligne" => $lignes[])
                ); 
    }
    
    public function formClientAction(Request $request)
    {
        $client = new Client();
        $compteclient = new CompteClient();
        /*$client->setID('id du client');;
        */
        $form = $this->createFormBuilder($client)
            ->add('nom', 'text')
            ->add('prenom', 'text')
            //->add('mail', 'text')
            ->add('codePostal', 'text')
            ->add('ville', 'text')
            ->getForm();

        return $this->render( array('form' => $form->createView(),
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

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
use Doctrine\DBAL\DriverManager;

class CommandeGroupeController extends Controller
{
     public function indexAction()
    {
        $recap = array();
        $idCommandeGroupee = $session->get('idCmdGroupee');
        $conn = DriverManager::getConnection($params, $config);
        $listeCommandeSimples = array();
        $sql = "SELECT produit.titre, produit.prix_ttc ,SUM( lignes.quantite ) ;
               FROM  produit ,  lignes ,  commande_contient_lignes ,  composition_cmd_groupee 
               WHERE composition_cmd_groupee.id_cmd_groupee = :idCmdG
               AND composition_cmd_groupee.id_cmd_simple = commande_contient_lignes.id_commande
               AND commande_contient_lignes.id_ligne = lignes.id_ligne
               AND lignes.id_produit = produit.id_produit
               GROUP BY produit.titre
               LIMIT 0 , 30";
        $recap = $conn->prepare($sql);
        $recap->bindValue("idCmdG", $idCommandeGroupee);
        $recap->execute();         
        // $recap contient : le nom du produit, son prix unitaire et la quantité pour toute la commande groupée
        
        $idClt = $session->get('idClient');
        $manageur = $this->getDoctrine()->getManager();
        $client = $manageur->getRepository("UserBundle:Client")->find($idClt);
        $compteClient = $manageur->getRepository("UserBundle:CompteClient")->find($client.idCompteClient);
        $infoClient = array();
        $infoClient["nom"]= $client.nom;
        $infoClient["prenom"]= $client.prenom;
        $infoClient["mail"]= $compteClient.email;
        $infoClient["cp"]= $client.codePostal;
        $infoClient["ville"]= $client.vile;
        // $infoClient contient les informations du user qui est connecté
        
        $livraison = array();
        $livraison = $this->getRequest("LivraisonBundle")->findByidCommande($idCommandeGroupee);
        
        
        
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeGroupeVPC.html.twig', array('form' => 
            $form->createView()), array("tab" => $tab)
                ); 
    }
 
}

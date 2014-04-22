<?php

namespace VieilleSardine\CommandeBundle\Controller;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SuiviCommandeController extends Controller
{
     public function indexAction()
    {
         /*
          *     $manageur = $this->getDoctrine()->getManager();
    $listecommandes = $manageur->getRepository("CommandeBundle:Commande")->findAll();
          */
         // récupérer toutes les commandes & ID & date
         $requete = Doctrine_Query::create() // On crée une requête.
		   ->from('commande') // On veut les enregistrements de la table commande.
		   ->execute(); // On exécute la requête.
         foreach($requete as $cmd) {
        // On ajoute la commande dans l'array.
             $cmds[] = $cmd;
         }
        return $this->render('VieilleSardineCommandeBundle:Commande:SuiviCommande.html.twig', array ('cmds'=> $cmds));
    }
    
    




}
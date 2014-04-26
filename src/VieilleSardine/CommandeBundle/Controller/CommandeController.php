<?php

namespace VieilleSardine\CommandeBundle\Controller;

use VieilleSardine\CommandeBundle\Entity\Lignes;
use VieilleSardine\ProduitBundle\Entity\Produit;
use VieilleSardine\UserBundle\Entity\CompteClient;
use VieilleSardine\UserBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class CommandeController extends Controller
{
    public function indexAction()
    {
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeVPC.html.twig');
    }
    
    // Méthode qui créer le formulaire utilisé pour ajouter un produit à la commande
    public function CreerFormVPCAction(Request $request)
    {
       
        // Requête utilisée pour l'autocomplétion
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery(
            'SELECT p.idProduit, p.titre, p.prixTtc
            FROM VieilleSardineProduitBundle:Produit p'
        )   ;

  
        $products = $query->getResult();
  
        $prod=array();
        $i=0;
        foreach($products as $val){
            $prod2=array();
            foreach($val as $index=>$val2){
                $prod2[$index]= htmlspecialchars($val2);
            }
            $prod[$i]=$prod2;
            
            $i++;
        }
      // Si le formulaire est valide, on créer une ligne et on la persiste en BD
        $UneLigne = new Lignes();
        $form = $this->createFormBuilder($UneLigne)
            ->add('idProduit', 'text')
            ->add('quantite', 'number')
            ->add('Ajouter', 'submit')
            ->getForm();
        
         $form->handleRequest($request);
         
         if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($UneLigne);
            $em->flush();
           
           
         }
           
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeVPC.html.twig', array('form' => 
            $form->createView(),"produits"=>$prod,
        ));
       
            
        
    }
    
    // Méthode pour retrouver les attributs d'un produit
    public function tabJsAction()
    {
        
        
         // return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeVPC.html.twig' , array('products'=>$products));
    }
    
       

    
    // Méthode de recherche utilisée par l'autocomplétion
    public function findIdProduitLike( $term, $limit = 10 )
    {
 
	$qb = $this->createQueryBuilder('c');
	$qb ->select('c.idProduit')
	->where('c.idProduit LIKE :term')
	->setParameter('term', '%'.$term.'%')
	->setMaxResults($limit);
 
	$arrayAss= $qb->getQuery()
	->getArrayResult();
 
	// Transformer le tableau associatif en un tableau standard
	$array = array();
	foreach($arrayAss as $data)
	{
		$array[] = array("IdProduit"=>$data['idProduit']);
	}
 
	return $array;
    }
    
    
    
     // Méthode qui créer le formulaire utilisé pour ajouter un produit à la commande
    public function CreerFormIDClientAction(Request $request)
    {
        // Requête SQL utilisée pour l'autocomplétion
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery(
            'SELECT c.idClient, c.nom, c.prenom, c.numeroVoie, c.typeVoie, c.codePostal, c.ville, c.pays
            FROM VieilleSardineUserBundle:Client c'
        )   ;

        $Clients = $query->getResult();
      
        $client=array();
        $i=0;
        foreach($Clients as $val){
            $client2=array();
            foreach($val as $index=>$val2){
                $client2[$index]= htmlspecialchars($val2);
            }
            $client[$i]=$client2;
            
            $i++;
        }
        
        $UnClient = new Client();
        $form = $this->createFormBuilder($UnClient)
            ->add('idClient','text')
            ->add('nom', 'text')
            ->add('prenom', 'text')
            ->add('numeroVoie', 'number')
            ->add('typeVoie', 'text')
            ->add('codePostal', 'text')
            ->add('ville', 'text')
            ->add('pays', 'text')
       
            ->add('Valider', 'submit')
            ->getForm();
        
         $form->handleRequest($request);
          if ($form->isValid()) {
              // Si le formulaire est valide, on crée la commande et la confirmation de commande
            $LaCommande = new Commande();
            $LaCommande->setDateCommande(date());
            $LaCommande->setEtatCommande("En Attente de préparation");
            $LaCommande->setPourcentageRemise("0");
            $LaCommande->setEstGroupee(false);
            $LaCommande->setMontant();
            
            $ConfirmationCommande = new ConfirmationDeCommande();
            $ConfirmationCommande->setObjet("Confirmation Commande");
            $ConfirmationCommande->setDestinataire($UnClient->getIdClient());
            $ConfirmationCommande->setDateCommande(date());
            $ConfirmationCommande->setMontantCommande($LaCommande->getMontantCommande());
            $ConfirmationCommande->setIdCommande($LaCommande->getIdCommande());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($LaCommande);
            $em->persist($ConfirmationCommande);
            $em->flush();
            
            return $this->redirect($this->generateUrl('RecapitulatifCommande'));
           
           
         }
         
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMClientVPC.html.twig', array('form' => 
            $form->createView(),"clients"=>$client,
        )); 
    }

    public function CreerFormRecapClient(Request $request)
    {
          return $this->render('VieilleSardineCommandeBundle:Commande:IHMRecapCommande.html.twig'
        ); 
    }


}

<?php

namespace VieilleSardine\CommandeBundle\Controller;

use VieilleSardine\CommandeBundle\Entity\Lignes;
use VieilleSardine\ProduitBundle\Entity\Produit;
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
      
        $UneLigne = new Lignes();
        $form = $this->createFormBuilder($UneLigne)
            ->add('idProduit', 'text')
            ->add('quantite', 'number')
            ->add('Ajouter', 'submit')
            ->getForm();
        
         $form->handleRequest($request);

        return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeVPC.html.twig', array('form' => 
            $form->createView(),
        ));
            
        
    }
    
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
    }
    
    // Méthode pour l'autocomplétion
     public function IdProduitAction()
    {
    	$request = $this->get('request');
 
    	if($request->isXmlHttpRequest())
    	{
    		$term = $request->request->get('idProduit');
    		$array= $this->getDoctrine()
    		->getEntityManager()
    		->getRepository('ProduitBundle:Produit')
    		->findIdProduitLike($term);
 
    		$response = new Response(json_encode($array));
    		$response -> headers -> set('Content-Type', 'application/json');
    		return $response;
    	}
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
        $UnClient = new Client();
        $form = $this->createFormBuilder($UnClient)
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
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMClientVPC.html.twig', array('form' => 
            $form->createView(),
        )); 
    }
    
    // Méthode JQuerry pour renseigner toutes les cases du produit en fonction de son id
    public function InfoProduitAction(Request $request)
    {
        $UnClient = new Client();
        $form = $this->createFormBuilder($UnClient)
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
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMClientVPC.html.twig', array('form' => 
            $form->createView(),
        )); 
    }

    



}

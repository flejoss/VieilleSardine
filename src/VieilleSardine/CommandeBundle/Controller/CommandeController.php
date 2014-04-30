<?php

namespace VieilleSardine\CommandeBundle\Controller;

use VieilleSardine\CommandeBundle\Entity\Lignes;
use VieilleSardine\CommandeBundle\Entity\Commande;
use VieilleSardine\CommandeBundle\Entity\ConfirmationDeCommande;
use VieilleSardine\ProduitBundle\Entity\Produit;
use VieilleSardine\UserBundle\Entity\CompteClient;
use VieilleSardine\UserBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommandeController extends Controller {

    public function indexAction() {
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeVPC.html.twig');
    }

    // Méthode qui créer le formulaire utilisé pour ajouter un produit à la commande
    public function CreerFormVPCAction(Request $request) {
        $session = $request->getSession();
        $nomClient = $session->get('client');
        echo "Commande de : " . $nomClient;
        
        //var_dump($nomClient);
        // Requête utilisée pour l'autocomplétion des champs du produit
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery(
                'SELECT p.idProduit, p.titre, p.prixTtc
            FROM VieilleSardineProduitBundle:Produit p'
        );


        $products = $query->getResult();

        $prod = array();
        $i = 0;
        foreach ($products as $val) {
            $prod2 = array();
            foreach ($val as $index => $val2) {
                // Enlever les caractères spéciaux
                $prod2[$index] = htmlspecialchars($val2);
            }
            $prod[$i] = $prod2;

            $i++;
        }
        $PrixTotal;
        // Si le formulaire est valide, on créer une ligne et on la persiste en BD
        $UneLigne = new Lignes();
        $form = $this->createFormBuilder($UneLigne)
                ->add('idProduit', 'text')
                ->add('quantite', 'number')
                ->add('Ajouter', 'submit')
                ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
//            var_dump($UneLigne);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($UneLigne);
            $em->flush();
        }
        // AJOUTER LE TABLEAU RECAP
        
        $form2 = $this->createFormBuilder()
                ->add('Libelle', 'text')
                ->add('Prix', 'text')
                ->add('PrixTotal', 'text')
//                ->add('listeProduit', 'collection', array('type' => 'Lignes'))
                ->add('Valider', 'submit')
                ->getForm();
        
        $form2->handleRequest($request);

        if ($form2->isValid()) {
            var_dump($prod);
              // Si le formulaire est valide, on crée la commande et la confirmation de commande
            $LaCommande = new Commande();
            $LaCommande->setDateCommande(date('Y-m-d'));
            $LaCommande->setEtatCommande("En Attente de préparation");
            $LaCommande->setPourcentageRemise("0");
            $LaCommande->setEstGroupee(false);
            

            $ConfirmationCommande = new ConfirmationDeCommande();
            $ConfirmationCommande->setObjet("Confirmation Commande");
            $ConfirmationCommande->setDestinataire($nomClient);
            $ConfirmationCommande->setDateCommande(date('Y-m-d'));
           // $ConfirmationCommande->setMontantCommande($LaCommande->getMontantCommande());
            $ConfirmationCommande->setIdCommande($LaCommande->getIdCommande());

            $em = $this->getDoctrine()->getManager();
            $em->persist($LaCommande);
            $em->persist($ConfirmationCommande);
            $em->flush();
            return $this->redirect($this->generateUrl('recap_commande_vpc'));
        }

        return $this->render('VieilleSardineCommandeBundle:Commande:IHMCommandeVPC.html.twig', array('form' =>
                    $form->createView(), 'form2' => $form2->createView(), "produits" => $prod,
        ));
    }

    // Méthode qui créer le formulaire utilisé pour ajouter un produit à la commande
    public function CreerFormIDClientAction(Request $request) {
        // Requête SQL utilisée pour l'autocomplétion
        $em = $this->getDoctrine()->getEntityManager();
        $query = $em->createQuery(
                'SELECT c.telPrincipal, c.nom, c.prenom, c.numeroVoie, c.typeVoie, c.codePostal, c.ville, c.pays
            FROM VieilleSardineUserBundle:Client c'
        );

        $Clients = $query->getResult();

        $client = array();
        $i = 0;
        foreach ($Clients as $val) {
            $client2 = array();
            foreach ($val as $index => $val2) {
                $client2[$index] = htmlspecialchars($val2);
            }
            $client[$i] = $client2;

            $i++;
        }

        // Données du client
        $UnClient = new Client();
        $form = $this->createFormBuilder($UnClient)
                ->add('telPrincipal', 'text')
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
            $session = $request->getSession();
            $session->set('client', $UnClient->getNom());
            
            return $this->redirect($this->generateUrl('ajout_produit_commande_vpc'));
        }

        return $this->render('VieilleSardineCommandeBundle:Commande:IHMClientVPC.html.twig', array('form' =>
                    $form->createView(), "clients" => $client,
        ));
    }

    // Méthode pour la vue du recap de la commande
    public function CreerFormRecapClient(Request $request) {
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMRecapCommande.html.twig'
        );
    }

}

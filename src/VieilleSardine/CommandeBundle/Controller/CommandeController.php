<?php

namespace VieilleSardine\CommandeBundle\Controller;

use VieilleSardine\CommandeBundle\Entity\Lignes;
use VieilleSardine\ProduitBundle\Entity\Produit;
use VieilleSardine\UserBundle\Entity\CompteClient;
use VieilleSardine\UserBundle\Entity\Client;
use Symfony\Component\HttpFoundation\Session\Session;
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
            $LaCommande->setEtatCommande("En Attente de préparation");
            $LaCommande->setPourcentageRemise("0");
            $LaCommande->setMontant(0);
            $LaCommande->setEstGroupee(false);


            $ConfirmationCommande = new ConfirmationDeCommande();
            $ConfirmationCommande->setObjet("Confirmation Commande");
            $ConfirmationCommande->setDestinataire($nomClient);
            $ConfirmationCommande->setMontantCommande(0);
            $ConfirmationCommande->setMessage("Coucou");
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

    public function CreerFormRecapClient(Request $request) {
        return $this->render('VieilleSardineCommandeBundle:Commande:IHMRecapCommande.html.twig'
        );
    }

    // ELIAS STYLE
    public function suiviCommandeAllAction() {
        $manageur = $this->getDoctrine()->getManager();
        $commandes = $manageur->getRepository("VieilleSardineCommandeBundle:Commande")->findAll();
        //var_dump($commandes);
        $tab = array();
        foreach ($commandes as $cmd) {
            //var_dump($cmd->getMontant());
            $client = $cmd->getIdClient();
            var_dump($client);
            //var_dump($client->getTelPrincipal());
            $tab[] = array("client" => $client->getPrenom(), "commandeID" => $cmd->getIdCommande(), "commandeDate" => $cmd->getDateCommande());
        }
        return $this->render('VieilleSardineCommandeBundle:Commande:SuiviCommande.html.twig', array("tab" => $tab));
    }

    public function detailCommandeAction($idCmd) {// il manque les frais de port ! où les trouver ?
        $manageur = $this->getDoctrine()->getManager();

        $commande = $manageur->getRepository("VieilleSardineCommandeBundle:Commande")->find($idCmd);
        var_dump($commande->getMontant());
        //$listeLignes = $manageur->getRepository("VieilleSardineCommandeBundle:Lignes")->findByidCommande();
        $listeLignes = $commande->getIdLigne();
        $tab = array();
        foreach ($listeLignes as $Ligne) {
            $pdt = $Ligne->getIdProduit();
            var_dump($pdt->getTitre());
            $pT = $pdt->getPrixHt() * $Ligne->getQuantite();
            $tab[] = array("produit" => $pdt->getTitre(), "quantite" => $Ligne->getQuantite(), "prixUnitaire" => $pdt->getPrixHt(), "prixTotal" => $pT);
        }
        var_dump($tab);
        $info = array();
        if ($commande->getEstGroupee()) {
            $info["type"] = "Groupée";
        } else {
            $info["type"] = "Simple";
        }
//         $info["remise"] = $commande->getPourcentageRemise();
        $info["montant"] = $commande->getMontant();
        $info["dateCommande"] = $commande->getDateCommande();
        $info["etat"] = $commande->getEtatCommande();
        return $this->render('VieilleSardineCommandeBundle:Commande:DetailCommande.html.twig', array("tab" => $tab, "info" => $info));
    }

    public function suiviCommandePersoAction() {
        $manageur = $this->getDoctrine()->getManager();
        $compteClient = $this->getUser();
        $id = $compteClient->getID();
        $client = $manageur->getRepository("VieilleSardineUserBundle:Client")->findOneByidCompteClient($id);
        //$listecommandes = $manageur->getRepository("VieilleSardineCommandeBundle:Commande")->findAll();
        $idClt = $client->getIdClient();
        var_dump($idClt);
        $tab = array();
        $commandesClient = $client->getIdCommande();
//        $listecommandes = $manageur->getRepository("VieilleSardineCommandeBundle:Commande")->findByidClient($idClt);
//        $listecommandes = $manageur->getRepository("VieilleSardineCommandeBundle:Commande")->findAll();
//        var_dump($listecommandes);
        foreach ($commandesClient as $cmd) {
            $tab[] = array("idCmd" => $cmd->getIdCommande(), "dateCmd" => $cmd->getDateCommande(), "etatCmd" => $cmd->getEtatCommande());
        }
        return $this->render('VieilleSardineCommandeBundle:Commande:SuiviCommandePerso.html.twig', array("tab" => $tab));
    }

}

<?php

namespace Coolloc\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;



class registerController
{

    //fonction d'analise des champs saisie
    public function registerAction(Application $app, Request $request)
    {
        //prénom : supérieur ou = a 2 inférieur a 50
        //nom : supérieur ou = a 2 inférieur a 50
        //date de naissance : avoir au moin 18ans
        //mdp : entre 6 et 20 caractère et mdp 1 = mdp 2
        //email : filter var
        //tel : type number et 10 caractère et rajouter +33 et si 9 chiffre ne rien suppr si 10 suppr le 1er
        //sexe : une des 2 value (femme / homme)
        //activité : au moin 1 activité de choisie
        //status : value soit cherche colocataire soit cherche colocation
        //condition : doit être égal a 1


    }

}

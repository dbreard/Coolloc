<?php

namespace Coolloc\Controller;


use Silex\Application;
use Coolloc\controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\TokensDAO;




class RegisterController extends Controller
{

    //fonction d'analyse des champs saisie

    public function registerAction(Application $app, Request $request)
    {
        $password = strip_tags(trim($request->get("password")));
        $password_repeat = strip_tags(trim($request->get("password_repeat")));
        $email = strip_tags(trim($request->get("mail")));
        $birthdate = strip_tags(trim($request->get("birthdate")));
        $birthdateFormatage = str_replace("-", "", $birthdate);
        $date = date("Ymd"); // date du jour
        $first_name = strip_tags(trim($request->get("firstname")));
        $last_name = strip_tags(trim($request->get("lastname")));
        $tel = strip_tags(trim($request->get("tel")));
        $sexe = strip_tags(trim($request->get("sex")));
        $activite = strip_tags(trim($request->get("activity")));
        $condition = strip_tags(trim($request->get("conditions")));
        $status = strip_tags(trim($request->get("status")));

        //prénom : supérieur ou = a 2 inférieur a 50
        (iconv_strlen($first_name) >= 2 && iconv_strlen($first_name) <= 50) ?: $this->erreur['firstname'] = 'Votre prénom doit être compris entre 2 et 50 caractères';


        //nom : supérieur ou = a 2 inférieur a 50
        (iconv_strlen($last_name) >= 2 && iconv_strlen($last_name) <= 50) ?: $this->erreur['lastname'] = 'Votre nom doit être compris entre 2 et 50 caractères';


        //date de naissance : avoir au moin 18ans
        (($date - $birthdateFormatage) > 180000) ?: $this->erreur['younger'] = 'Vous êtes trop jeune';


        //mdp : entre 6 et 20 caractère et mdp 1 = mdp 2


        ($this->verifCorrespondanceMdp($password, $password_repeat)) ?  : $this->erreur['password_correspondance'] = 'Les mots de passe ne correspondent pas';
        ($this->verifMdp($password)) ? $password = password_hash($password, PASSWORD_DEFAULT) . md5('bruh') : $this->erreur['password'] = 'Format mot de passe incorrect';



        //Vérification d'email :
        (!$this->verifEmail($email)) ?  : $verifEmailBdd = new UserModelDAO($app['db']);
        (isset($verifEmailBdd))? $resultat = $verifEmailBdd->verifEmailBdd($email) : $this->erreur['invalid_email'] = 'Email invalide' ;

        (empty($resultat)) ?  : $this->erreur['email_already_exist'] = 'L\'Email est deja utilisé';


        //tel : type number et 10 caractère et rajouter +33 et si 9 chiffre ne rien suppr si 10 suppr le 1er
        ($this->verifTel($tel)) ? $tel = $this->modifyTel($tel) : $this->erreur['incorrect_phone'] = 'Format de téléphone incorect';


        //sexe : une des 2 value (femme / homme)
        ($sexe == 'femme' || $sexe == 'homme') ?: $this->erreur['sex_error'] = 'veuillez préciser un sexe valide';


        //activité : au moin 1 activité de choisie
        ($activite == 'activité pro' || $activite == 'retraité' || $activite == 'sans activité' || $activite == 'étudiant') ?: $this->erreur['activity_error'] = 'veuillez préciser une activité valide';


        //condition : doit être égal a 1
        ($condition == 1) ?: $this->erreur['no_condition'] = 'veuillez accepter les conditions d\'utilisation';

        //Status égal à inactif
        ($status == 'inactif') ?: $this->erreur['accept_condition'] = 'veuillez accepter les conditions d\'utilisation';


        // SI IL Y A DES ERREURS
        if (!empty($this->erreur)) {
            return $app['twig']->render('formulaires/register.html.twig', array(
                "error" => $this->erreur,
                "email" => $email,
                "birthdate" => $birthdate,
                "firstname" => $first_name,
                "lastname" => $last_name,
                "tel" => $tel,
            ));
        } else { // SI IL N'Y A PAS D'ERREUR
            $insertUser = new UserModelDAO($app['db']);
            $idUser = $insertUser->insertUser($first_name, $last_name, $birthdate, $password, $email, $tel, $sexe, $activite, $condition);
        }


        // SI L'INSERTION NE C'EST PAS FAITE
        if (empty ($idUser))
        {
            return $app['twig']->render('formulaires/register.html.twig', array(
                "error" => 'Erreur lors de votre inscription veuillez réessayer svp',
            ));
        }
        // SI L'INSERTION C'EST BIEN PASSER ON GENERE UN TOKEN
        else
        {
            $token = $this->generateToken();
            $dateExpire = $this->expireToken();
            $tokens = new TokensDAO($app['db']);
            $tokenGenerate = $tokens->createToken($idUser, $dateExpire, $token, 'email');
        }

        if (!empty($tokenGenerate)) { // SI LA GENERATION DU TOKEN A BIEN ETE EFFECTUER ON ENVOIE LE MAIL DU TOKEN
            $this->sendMailToken(
                array("adress" => $email, "name" => $first_name),
                array("body" => "<!doctype html><html><head><meta name='viewport' content='width=device-width'>     <meta http-equiv='Content-Type' content='text/html'; charset='UTF-8'>     <title>Simple Transactional Email</title>     <style>     /* -------------------------------------         INLINED WITH htmlemail.io/inline     ------------------------------------- */     /* -------------------------------------         RESPONSIVE AND MOBILE FRIENDLY STYLES     ------------------------------------- */     @media only screen and (max-width: 620px) {       table[class=body] h1 {         font-size: 28px !important;         margin-bottom: 10px !important;       }       table[class=body] p,             table[class=body] ul,             table[class=body] ol,             table[class=body] td,             table[class=body] span,             table[class=body] a {         font-size: 16px !important;       }       table[class=body] .wrapper,             table[class=body] .article {         padding: 10px !important;       }       table[class=body] .content {         padding: 0 !important;       }       table[class=body] .container {         padding: 0 !important;         width: 100% !important;       }       table[class=body] .main {         border-left-width: 0 !important;         border-radius: 0 !important;         border-right-width: 0 !important;       }       table[class=body] .btn table {         width: 100% !important;       }       table[class=body] .btn a {         width: 100% !important;       }       table[class=body] .img-responsive {         height: auto !important;         max-width: 100% !important;         width: auto !important;       }     }     /* -------------------------------------         PRESERVE THESE STYLES IN THE HEAD     ------------------------------------- */     @media all {       .ExternalClass {         width: 100%;       }       .ExternalClass,             .ExternalClass p,             .ExternalClass span,             .ExternalClass font,             .ExternalClass td,             .ExternalClass div {         line-height: 100%;       }       .apple-link a {         color: inherit !important;         font-family: inherit !important;         font-size: inherit !important;         font-weight: inherit !important;         line-height: inherit !important;         text-decoration: none !important;       }       .btn-primary table td:hover {         background-color: #34495e !important;       }       .btn-primary a:hover {         background-color: #34495e !important;         border-color: #34495e !important;       }     }     </style>   </head>   <body class='' style='background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>     <table border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;'>       <tr>         <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>&nbsp;</td>         <td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;'>           <div class='content' style='box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;'>              <!-- START CENTERED WHITE CONTAINER -->             <span class='preheader' style='color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;'>This is preheader text. Some clients will show this text as a preview.</span>             <table class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;'>                <!-- START MAIN CONTENT AREA -->               <tr>                 <td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;'>                   <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;'>                     <tr>                       <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Bonjour et bienvenue sur Coolloc </p>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Pour valider l'adresse email de ce compte, veuillez clicker sur le lien ci-dessous</p>                         <table border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;'>                           <tbody>                             <tr>                               <td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>                                 <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;'>                                   <tbody>                                     <tr>                                       <td style='font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;'> <a href='http://localhost/Coolloc/public/verif/$token/' target='_blank' style='display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;'>Confirmer mon email</a> </td>                                     </tr>                                   </tbody>                                 </table>                               </td>                             </tr>                           </tbody>                         </table>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Cet email est genere automatiquement, merci de ne pas y repondre.</p>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Heureux de vous compter parmi nos effectifs !<br>La team Coolloc</p>                       </td>                     </tr>                   </table>                 </td>               </tr>              <!-- END MAIN CONTENT AREA -->             </table>              <!-- START FOOTER -->             <div class='footer' style='clear: both; Margin-top: 10px; text-align: center; width: 100%;'>               <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;'>                 <tr>                   <td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;'>                     <span class='apple-link' style='color: #999999; font-size: 12px; text-align: center;'>Company Inc, 3 Abbey Road, San Francisco CA 94102</span>                     <br> Don't like these emails? <a href='http://i.imgur.com/CScmqnj.gif' style='text-decoration: underline; color: #999999; font-size: 12px; text-align: center;'>Unsubscribe</a>.                   </td>                 </tr>                 <tr>                   <td class='content-block powered-by' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;'>                     Powered by <a href='http://htmlemail.io' style='color: #999999; font-size: 12px; text-align: center; text-decoration: none;'>HTMLemail</a>.                   </td>                 </tr>               </table>             </div>             <!-- END FOOTER -->            <!-- END CENTERED WHITE CONTAINER -->           </div>         </td>         <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>&nbsp;</td>       </tr>     </table>   </body> </html>", "subject" => 'Confirmation d\'adresse mail CoolLoc'));
        }

        return $app->redirect('/Coolloc/public/confirmation');
    }

    // FONCTIOON DE VERIFICATION EMAIL POUR INSCRIPTION
    public function verifEmailAction (Application $app, Request $request){
        $token = strip_tags(trim($request->get("token"))); // ON RECUPERE LE TOKEN DANS L'URL

        $selectToken = new tokensDAO($app['db']);
        $validateToken = $selectToken->verifValidateToken($token);

        $selectUser = new UserModelDAO($app['db']);
        $idUser = $selectUser->selectUserFromToken($token);


        if(!$idUser) // SI AUCUN UTILISATEUR NE CORRESPOND AU TOKEN
            return $app['twig']->render('formulaires/login.html.twig', array(
                "error" => 'Erreur lors de la validation de l\'email veuillez ressayer',
            ));


        if(!$validateToken){ // SI LA DATE DE VALIDITER DU TOKEN N'EST PAS BONNE
            $selectToken->deleteToken($token); // on delete l'ancien token
            $token = $this->generateToken(); // on en genere un nouveau
            $dateExpire = $this->expireToken();
            $tokenGenerate = $selectToken->createToken($idUser['user_id'], $dateExpire, $token, 'email');

            // SI LA GENERATION DU TOKEN A BIEN ETE EFFECTUER ON ENVOIE LE MAIL DU TOKEN
            if (!empty($tokenGenerate)) {
                $user = $selectUser->selectUserFromId($idUser['user_id']);

                $this->sendMailToken(
                    array("adress" => $user['mail'], "name" => $user['firstname']),
                    array("body" => "<!doctype html><html><head><meta name='viewport' content='width=device-width'>     <meta http-equiv='Content-Type' content='text/html'; charset='UTF-8'>     <title>Simple Transactional Email</title>     <style>     /* -------------------------------------         INLINED WITH htmlemail.io/inline     ------------------------------------- */     /* -------------------------------------         RESPONSIVE AND MOBILE FRIENDLY STYLES     ------------------------------------- */     @media only screen and (max-width: 620px) {       table[class=body] h1 {         font-size: 28px !important;         margin-bottom: 10px !important;       }       table[class=body] p,             table[class=body] ul,             table[class=body] ol,             table[class=body] td,             table[class=body] span,             table[class=body] a {         font-size: 16px !important;       }       table[class=body] .wrapper,             table[class=body] .article {         padding: 10px !important;       }       table[class=body] .content {         padding: 0 !important;       }       table[class=body] .container {         padding: 0 !important;         width: 100% !important;       }       table[class=body] .main {         border-left-width: 0 !important;         border-radius: 0 !important;         border-right-width: 0 !important;       }       table[class=body] .btn table {         width: 100% !important;       }       table[class=body] .btn a {         width: 100% !important;       }       table[class=body] .img-responsive {         height: auto !important;         max-width: 100% !important;         width: auto !important;       }     }     /* -------------------------------------         PRESERVE THESE STYLES IN THE HEAD     ------------------------------------- */     @media all {       .ExternalClass {         width: 100%;       }       .ExternalClass,             .ExternalClass p,             .ExternalClass span,             .ExternalClass font,             .ExternalClass td,             .ExternalClass div {         line-height: 100%;       }       .apple-link a {         color: inherit !important;         font-family: inherit !important;         font-size: inherit !important;         font-weight: inherit !important;         line-height: inherit !important;         text-decoration: none !important;       }       .btn-primary table td:hover {         background-color: #34495e !important;       }       .btn-primary a:hover {         background-color: #34495e !important;         border-color: #34495e !important;       }     }     </style>   </head>   <body class='' style='background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>     <table border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;'>       <tr>         <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>&nbsp;</td>         <td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;'>           <div class='content' style='box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;'>              <!-- START CENTERED WHITE CONTAINER -->             <span class='preheader' style='color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;'>This is preheader text. Some clients will show this text as a preview.</span>             <table class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;'>                <!-- START MAIN CONTENT AREA -->               <tr>                 <td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;'>                   <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;'>                     <tr>                       <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Bonjour et bienvenue sur Coolloc </p>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Pour valider l'adresse email de ce compte, veuillez clicker sur le lien ci-dessous</p>                         <table border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;'>                           <tbody>                             <tr>                               <td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>                                 <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;'>                                   <tbody>                                     <tr>                                       <td style='font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;'> <a href='http://localhost/Coolloc/public/verif/$token/' target='_blank' style='display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;'>Confirmer mon email</a> </td>                                     </tr>                                   </tbody>                                 </table>                               </td>                             </tr>                           </tbody>                         </table>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Cet email est genere automatiquement, merci de ne pas y repondre.</p>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Heureux de vous compter parmi nos effectifs !<br>La team Coolloc</p>                       </td>                     </tr>                   </table>                 </td>               </tr>              <!-- END MAIN CONTENT AREA -->             </table>              <!-- START FOOTER -->             <div class='footer' style='clear: both; Margin-top: 10px; text-align: center; width: 100%;'>               <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;'>                 <tr>                   <td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;'>                     <span class='apple-link' style='color: #999999; font-size: 12px; text-align: center;'>Company Inc, 3 Abbey Road, San Francisco CA 94102</span>                     <br> Don't like these emails? <a href='http://i.imgur.com/CScmqnj.gif' style='text-decoration: underline; color: #999999; font-size: 12px; text-align: center;'>Unsubscribe</a>.                   </td>                 </tr>                 <tr>                   <td class='content-block powered-by' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;'>                     Powered by <a href='http://htmlemail.io' style='color: #999999; font-size: 12px; text-align: center; text-decoration: none;'>HTMLemail</a>.                   </td>                 </tr>               </table>             </div>             <!-- END FOOTER -->            <!-- END CENTERED WHITE CONTAINER -->           </div>         </td>         <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>&nbsp;</td>       </tr>     </table>   </body> </html>", "subject" => 'Confirmation d\'adresse mail CoolLoc'));
            }

        return $app['twig']->render('formulaires/login.html.twig', array(
                "error" => 'La date de validité de votre mail a expiré. Un mail vous a été réenvoyer afin de valider votre compte.',

            ));

        }

        // MODIFICATION DU STATUS DE L'UTILISATEUR
        $rowAffected = $selectUser->updateUserFromToken($idUser);


        if($rowAffected == 1) { // SI L'UTILISATEUR A BIEN ETE MODIFIER
            $rowAffectedDeleteToken = $selectToken->deleteToken($token);
        }


        if ($rowAffectedDeleteToken == 1){ // SI LE TOKEN A BIEN ETE SUPPRIMER
          return $app->redirect('/Coolloc/public/login');
        }
    }

}

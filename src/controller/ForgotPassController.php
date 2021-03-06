<?php

namespace Coolloc\Controller;

use Silex\Application;
use Coolloc\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\Model;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\TokensDAO;

class ForgotPassController extends Controller
{

    //fonction d'analyse des champs saisie
    public function forgotPassAction(Application $app, Request $request)
    {
        $email = strip_tags(trim($request->get("mail")));
        $first_name = strip_tags(trim($request->get("firstname")));

        //email : filter var
        //email : doit être égal à un email register
        //email : envoie email

        ($this->verifEmail($email)) ?  : $this->erreur['format'] = 'Format de l\'email incorrect';


        if (!empty($this->erreur)) {
            return $app['twig']->render('basic/forgotten-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }

        else
        {
            $resultat = new UserModelDAO($app['db']);
            $user = $resultat->verifEmailBdd($email);

            ($user)? : $this->erreur['mail'] = 'L\'email n\'est pas associé à un compte';

            if (!empty($this->erreur)) {
                return $app['twig']->render('basic/forgotten-password.html.twig', array(
                    "error" => $this->erreur,
                ));
            }

            $tokens = new TokensDAO($app['db']);

            $existToken = $tokens->verifExistTokenEmail($user['id_user']); // verifie si le token existe deja en bdd

            if($existToken){ // si un ancien token correspondant a l'utilisateur existe
                $token = $tokens->selectTokenFromIdUser($user['id_user']);
                $tokens->deleteToken($token['token']);
            }

                if (!empty($user))
                { // Si la selection s'est bien passée
                    $token = $this->generateToken();
                    $dateExpire = $this->expireToken();
                    $tokenGenerate = $tokens->createToken($user['id_user'], $dateExpire, $token, 'email');

                    if (!empty($tokenGenerate))
                    { // SI LA GENERATION DU TOKEN A BIEN ETE EFFECTUER ON ENVOIE LE MAIL DU TOKEN
                        $resultatEmail = $this->sendMailToken(
                            array("adress" => $email, "name" => $first_name),
                            array("body" => "<!doctype html> <html>   <head>     <meta name='viewport' content='width=device-width'>     <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>     <title>Simple Transactional Email</title>     <style>     /* -------------------------------------         INLINED WITH htmlemail.io/inline     ------------------------------------- */     /* -------------------------------------         RESPONSIVE AND MOBILE FRIENDLY STYLES     ------------------------------------- */     @media only screen and (max-width: 620px) {       table[class=body] h1 {         font-size: 28px !important;         margin-bottom: 10px !important;       }       table[class=body] p,             table[class=body] ul,             table[class=body] ol,             table[class=body] td,             table[class=body] span,             table[class=body] a {         font-size: 16px !important;       }       table[class=body] .wrapper,             table[class=body] .article {         padding: 10px !important;       }       table[class=body] .content {         padding: 0 !important;       }       table[class=body] .container {         padding: 0 !important;         width: 100% !important;       }       table[class=body] .main {         border-left-width: 0 !important;         border-radius: 0 !important;         border-right-width: 0 !important;       }       table[class=body] .btn table {         width: 100% !important;       }       table[class=body] .btn a {         width: 100% !important;       }       table[class=body] .img-responsive {         height: auto !important;         max-width: 100% !important;         width: auto !important;       }     }     /* -------------------------------------         PRESERVE THESE STYLES IN THE HEAD     ------------------------------------- */     @media all {       .ExternalClass {         width: 100%;       }       .ExternalClass,             .ExternalClass p,             .ExternalClass span,             .ExternalClass font,             .ExternalClass td,             .ExternalClass div {         line-height: 100%;       }       .apple-link a {         color: inherit !important;         font-family: inherit !important;         font-size: inherit !important;         font-weight: inherit !important;         line-height: inherit !important;         text-decoration: none !important;       }       .btn-primary table td:hover {         background-color: #34495e !important;       }       .btn-primary a:hover {         background-color: #34495e !important;         border-color: #34495e !important;       }     }     </style>   </head>   <body class='' style='background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;'>     <table border='0' cellpadding='0' cellspacing='0' class='body' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;'>       <tr>         <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>&nbsp;</td>         <td class='container' style='font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;'>           <div class='content' style='box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;'>              <!-- START CENTERED WHITE CONTAINER -->             <span class='preheader' style='color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;'>This is preheader text. Some clients will show this text as a preview.</span>             <table class='main' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;'>                <!-- START MAIN CONTENT AREA -->               <tr>                 <td class='wrapper' style='font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;'>                   <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;'>                     <tr>                       <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Bonjour,</p>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Vous souhaitez modifier votre mot de passe, suite a un oublie.</p>                         <table border='0' cellpadding='0' cellspacing='0' class='btn btn-primary' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box;'>                           <tbody>                             <tr>                               <td align='left' style='font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;'>                                 <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;'>                                   <tbody>                                     <tr>                                       <td style='font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: #3498db; border-radius: 5px; text-align: center;'> <a href='http://localhost/Coolloc/public/change-password/$token' target='_blank' style='display: inline-block; color: #ffffff; background-color: #3498db; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize; border-color: #3498db;'>Changer de MDP</a> </td>                                     </tr>                                   </tbody>                                 </table>                               </td>                             </tr>                           </tbody>                         </table>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>Si le bouton ne fonctionne pas, cliquez ci-dessous</p>                         <p style='font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;'>http://localhost/Coolloc/public/change-password/$token</p>                       </td>                     </tr>                   </table>                 </td>               </tr>              <!-- END MAIN CONTENT AREA -->             </table>              <!-- START FOOTER -->             <div class='footer' style='clear: both; Margin-top: 10px; text-align: center; width: 100%;'>               <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;'>                 <tr>                   <td class='content-block' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;'>                     <span class='apple-link' style='color: #999999; font-size: 12px; text-align: center;'>Merci de faire confiance a CoolLoc</span>                   </td>                 </tr>                 <tr>                   <td class='content-block powered-by' style='font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 10px; font-size: 12px; color: #999999; text-align: center;'></td>                 </tr>               </table>             </div>             <!-- END FOOTER -->            <!-- END CENTERED WHITE CONTAINER -->           </div>         </td>         <td style='font-family: sans-serif; font-size: 14px; vertical-align: top;'>&nbsp;</td>       </tr>     </table>   </body> </html>", "subject" => 'Changement de mot de passe CoolLoc'));
                            (!empty($resultatEmail)) ?  : $this->erreur['send'] = 'Email pas envoyé';
                    }
                }
        }

        if(empty($this->erreur))
        {
            return $app['twig']->render('confirmation-oublie.html.twig');
        }
        else
        {
            return $app['twig']->render('basic/forgotten-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }
    }

    // fonction qui vérifie le token et renvoie vers la page modification de mdp
    public function verifEmailForgotAction (Application $app, Request $request)
    {
        $token = strip_tags(trim($request->get("token"))); // ON RECUPERE LE TOKEN DANS L'URL

        $selectUser = new UserModelDAO($app['db']);
        $idUser = $selectUser->selectUserFromToken($token);

        if (!$idUser) {// SI AUCUN UTILISATEUR NE CORRESPOND AU TOKEN
            $this->erreur['validChange'] = 'Erreur lors de la validation de l\'email veuillez réessayer';

            return $app['twig']->render('formulaires/forgotten-password.html.twig', array(
                "error" => $this->erreur,
            ));
        }else {
            return $app->redirect("/Coolloc/public/change-password/");
        }
    }


}

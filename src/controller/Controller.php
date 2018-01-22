<?php

namespace Coolloc\Controller;

use Silex\Application;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Coolloc\Model\UserModelDAO;
use Coolloc\Model\TokensDAO;
use \DateTime;






class Controller {

    public $URL = 'http://localhost/Coolloc/public/';
    protected $erreur = array();
    private $token;

    // VERIFICATION DU FORMAT EMAIL
    public function verifEmail(string $email) :bool
    {
        $resultat = (filter_var($email, FILTER_VALIDATE_EMAIL)) ? true : false;
        return $resultat;
    }

    // VERIFICATION DU FORMAT MDP
    public function verifMdp(string $password)
    {
       $resultat = (iconv_strlen($password) >= 6 && iconv_strlen($password) <= 20) ? true : false ;
           return $resultat;
    }

    // MODIFICATION DU FORMAT NUMERO DE TELEPHONE
    public function modifyTel(string $tel){

        if (iconv_strlen($tel) == 10){
            $tel = "+33" . (substr($tel, 1));
        }
        else if (iconv_strlen($tel) == 12){
            $tel = "+33" . (substr($tel, 3));
        }
        else if (iconv_strlen($tel) == 9){
            $tel = "+33" . (substr($tel, 0));
        }

        return $tel;
    }
    //VERIFICATION DU FORMAT DE NUMERO DE TELEPHONE
    public function verifTel(string $tel) :bool
    {
        $tel = str_replace(" ", "", $tel);
        $tel = str_replace("-", "", $tel);
        $tel = str_replace("/", "", $tel);

        if (iconv_strlen($tel) == 10){
            $resultat = (substr($tel, -10, 1) == 0) ? true : false;
        }
        elseif (iconv_strlen($tel) == 12){
            $resultat = (substr($tel, -12, 3) == "+33") ? true : false;
        }
        elseif (iconv_strlen($tel) == 9){
            $resultat = (substr($tel, -9, 1) == 0) ? true : false;
        }
        else{
            $resultat = false;
        }
        return $resultat;
    }


    //VERIFICATION DE LA CORRESPONDANCE DES MOT DE PASSE
    public function verifCorrespondanceMdp(string $password,string $password_repeat) : bool
    {
        $resultat = ($password == $password_repeat) ? true : false;
        return $resultat;
    }

    //CREATION DE TOKEN
    public function generateToken() {
        return substr( md5( uniqid().mt_rand() ), 0, 22 );
    }

    // FONCTION DE DATE LIMITE DU TOKEN
    public function expireToken(){
        $dateNow = new DateTime();
        $dateNow->modify("+ 1 day");
        return $dateNow->format("Y-m-d H:i:s");
    }

    // FONCTION D'ENVOIE DE MAIL DE TOKEN
    public function sendMailToken(array $user,array $message = array()) {
        global $app;
        try {
            //Server settings
            $mail = $app['mail'];
            $mail->SMTPDebug = 0;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp-mail.outlook.com';              // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'coollocstaff@outlook.fr';                 // SMTP username
            $mail->Password = 'azerty1234';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('coollocstaff@outlook.fr', 'Coolloc Staff');
            $mail->addAddress($user["adress"], $user["name"]);

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $message["subject"];
            $mail->Body    = $message["body"];

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    // VERIFIE SI L'UTILISATEUR EST CONNECTER
    public function verifConnected(Application $app, Request $request){
        $pageName = strip_tags(trim($request->get("pagename")));
        if (isset($_SESSION['membre']['zoubida']) && !empty($_SESSION['membre']['zoubida'])) {
            return $app->redirect('/Coolloc/public/connected/' . $pageName);
        }else {
            return $app->redirect('/Coolloc/public/login');
        }
    }

    // VERIFIE SI L'UTILISATEUR EST CONNECTER ET ADMIN
    public function verifConnectedAdmin(Application $app, Request $request){
        $pageName = strip_tags(trim($request->get("pagename")));
        if (isset($_SESSION['membre']['zoubida']) && !empty($_SESSION['membre']['zoubida']) && $_SESSION['membre']['status'] == 'admin' ) {
            return $app->redirect('/Coolloc/connected/admin/' . $pageName);
        }else {
            return $app->redirect('/Coolloc/public/login');
        }

    }

    public static function ifConnected(){
        if (isset($_SESSION['membre']['zoubida']) && !empty($_SESSION['membre']['zoubida'])) {
            return true;
        }else {
            return false;
        }
    }

    public static function ifConnectedAndAdmin(){
        if (isset($_SESSION['membre']['zoubida']) && !empty($_SESSION['membre']['zoubida']) && $_SESSION['membre']['status'] == 'admin') {
            return true;
        }else {
            return false;
        }
    }

    public function sessionDestroy(Application $app, Request $request) {


        // On delete le token de connexion
        $deleteTokenConnection = new TokensDAO($app['db']);
        $deleteTokenConnection->deleteToken($_SESSION['membre']['zoubida']);

        // Détruit toutes les variables de session
        $_SESSION = array();

        // Finalement, on détruit la session.
        session_destroy();

        // var_dump($_SESSION);

        // On redirige vers l'acceuil
        return $app->redirect('/Coolloc/public');
    }


    // FORMATAGE DE LA CITY POUR LA RENDRE CONFORME A LA BDD
    public function formatCity(string $city) {
        $cityClean = strip_tags(trim($city));
        $cityClean = ucwords ($cityClean);
        $cityClean = str_replace(" ", "-", $cityClean);
        $cityClean = str_replace("_", "-", $cityClean);
        return $cityClean;
    }

    // VERIF DES CHAMPS EN OUI/NON/PEU IMPORTE
    public function validSelect(string $champs, string $name) {

        // $champs => le champs a vérifier ($request->get(''))
        // $name => Pour personnalisé le message d'erreur

        $champs = strip_tags(trim($champs));
        if ($champs != 'oui' && $champs != 'non' && $champs != 'peu importe') {
            array_push($this->erreur, "Saisie incorrecte sur le champs '" . $name . "'");
        }else {
            return $champs;
        }
    }


    // FONCTION POUR VERIFIER LA VALIDITE D'UN ARRAY ET FORMATER CELUI CI POUR LA BDD
    public function verifArrayAndFormat(array $arrayTarget, array $arrayCompare, string $champs, string $mode): string {
        // on créer un tableau pour faire le comparatif
        $arrayCheck = array();
        // boucle sur l'ensemble des données
        foreach ($arrayTarget as $key => $value) {
            // on nettoi chaque valeur
            $value = strip_tags(trim($value));
            // pour chaque valeur on vérifie si elle est valide
            foreach ($arrayCompare as $key2 => $value2) {
                // Si oui on check
                if ($value == $value2) {
                    array_push($arrayCheck, "check");
                }
            }
        }
        // Si le nombre de valeur ne correspond pas au nombre de check erreur
        if (count($arrayCheck) != count($arrayTarget)) {

            if ($champs == "Quartier") {
                $this->erreur['district'] = "Problème de selection dans '" . $champs . "'";
            }else if ($champs == "Equipement") {
                $this->erreur['equipment'] = "Problème de selection dans '" . $champs . "'";
            }else if ($champs == "Profil colocataires") {
                $this->erreur['member_profil'] = "Problème de selection dans '" . $champs . "'";
            }else if ($champs == "Centre d'intérêts") {
                $this->erreur['hobbies'] = "Problème de selection dans '" . $champs . "'";
            }

            return "";
        }else { // Sinon on format notre tableau en string en fonction du mode choisi
            // Si le mode est SELECT
            if ($mode == "SELECT") {
                // On vérifie le champs pour nommé le champsBDD
                if ($champs == "Quartier") {
                    // On appelle la fonction avec le champsBDD
                    $response = $this->searchByArray($arrayTarget, "district");
                }else if ($champs == "Equipement") {
                    $response = $this->searchByArray($arrayTarget, "equipment");
                }else if ($champs == "Profil colocataires") {
                    $response = $this->searchByArray($arrayTarget, "member_profil");
                }else if ($champs == "Centre d'intérêts") {
                    $response = $this->searchByArray($arrayTarget, "hobbies");
                }else {
                    echo "Champs saisi " . $champs . " invalide dans la fonction verifArrayAndFormat()";
                    die();
                }
                return $response;
            }else if ($mode == "INSERT") {
                $response = $this->formatArrayForBDD($arrayTarget);
                return $response;
            }else {
                echo "Erreur de selection de mode dans la fonction verifArrayAndFormat() : 'SELECT' ou 'INSERT'";
                die();
            }
        }
    }

    // FONCTION APPELER PAR verifArrayAndFormat() SI LE MODE CHOISI EST "SELECT" POUR CHERCHER EN BDD VIA UN TABLEAU
    private function searchByArray(array $arrayTarget, string $champsBDD): string {
        // Je crée ma variable réponse
        $response = "";
        // Je vérifie que mon tableau n'es pas vide
        if (count($arrayTarget) != 0) {
            // Je boucle sur mes données
            foreach ($arrayTarget as $key => $value) {
                $response .= 'AND ' . $champsBDD . ' LIKE "%' . $value . '%" ';
            }
            return $response;
        }else {
            return $response;
        }
    }

    // FONCTION APPELER PAR verifArrayAndFormat() SI LE MODE CHOISI EST "INSERT" POUR VALIDER ET FORMATER UN ARRAY EN STRING POUR l'INSERTION
    private function formatArrayForBDD(array $arrayTarget): string {
        // Je créer ma variable de réponse
        $response = "";
        // Je vérifie que mon tableau n'est pas vide
        if (count($arrayTarget) != 0) {
            // Je boucle sur toutes les données
            foreach ($arrayTarget as $key => $value) {
                // Si c'est la première entrée
                if ($key == 0) {
                    $response .= $value;
                }else { // Sinon
                    $response .= ", " . $value;
                }
            }
            // Je renvoie ma réponse avec le tableau traduit en string
            return $response;
        }else {
            // sinon je renvoie ma réponse avec une string vide
            return $response;
        }
    }


    //********** GETTER ***********//

    public function getDate(){
        return date("Ymd");
    }

    //*********** GETTER ****************//

    public function getToken(){
        return $this->token;
    }

    //*********** SETTER ****************//

    public function setToken($token){
        $this->token = $token;
    }

    //-----------------------ENVOI DE MAILS AU STAFF--------------------------//

    public function sendMailStaff(string $user, array $message): bool{
      try {


        global $app;
        $mail = $app['mail'];
        //Server settings
        $mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp-mail.outlook.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'coollocstaff@outlook.fr';           // SMTP username
        $mail->Password = 'azerty1234';                    // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('coollocstaff@outlook.fr', $user);
        $mail->addAddress('coollocstaff@outlook.fr', 'CoollocStaff');

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $message['subject'];
        $mail->Body    = $message['body'];


        $mail->send();
        return true;
      }
      catch (Exception $e) {
        return false;
      }
    }
    // --------------------- fin envoi mail ----------------------- //

    // fonction permettant de formater les champs multiple string en array
    public static function stringToArray(string $stringTarget){


        if (empty($stringTarget))
            return '';

        $arrayConstruct = explode(', ', $stringTarget);

        foreach ($arrayConstruct as $key => $value) {

            $newKey = str_replace(' ', '', $value);
            $newKey = str_replace("'", '', $newKey);
            $newKey = str_replace("-", '', $newKey);
            $arrayResponse[$newKey] = $value;

        }

        return $arrayResponse;

    }

}

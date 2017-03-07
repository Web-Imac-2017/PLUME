<?php

require_once(__DIR__.'/../Database.php');

class UserModel {

    public static function getPseudoById($id) {
        $bdd = Database::connexionBDD();
        
        $req_ident = $bdd->prepare('SELECT pseudo FROM user WHERE `ID` = "'.$id.'"');
        $req_ident->execute();

        $result = $req_ident->fetch(PDO::FETCH_ASSOC);
        
        return $result['pseudo'];
    }

   public static function login($pseudo, $password) {
        $bdd = Database::connexionBDD();
        
        $req_ident = $bdd->prepare('SELECT pseudo FROM user WHERE pseudo = "'.$pseudo.'"');
        $req_ident->execute();

        if($req_ident->fetch()){
            $req_password = $bdd->prepare('SELECT password FROM user WHERE pseudo = "'.$pseudo.'"');
            $req_password->execute();
            $recup_password = $req_password->fetch();
            $password_hach = md5($password);
                    
            if($password_hach == $recup_password["password"]){
                UserModel::setUserState($pseudo, 1);

                $req_id = $bdd->prepare('SELECT ID FROM user WHERE pseudo = "'.$pseudo.'"');
                $req_id->execute();
                $idUser = $req_id->fetch(PDO::FETCH_ASSOC);

                $result = array($idUser['ID'], $pseudo);

                /*ob_start();
                setcookie("PLUME_pseudo", $pseudo);
                ob_end_flush();

                print_r($_COOKIE['PLUME_pseudo']);*/
            }
            else {
                $result = array("Error", "Error: the password isn't correct.");
            }
        }
        else {
            $result = array("Error", "Error: the pseudo doesn't exist.");
        }
        
        return $result;
    }

    public static function setUserState($pseudo, $userState) {
        $bdd = Database::connexionBDD();
       
        $req_active = $bdd->prepare('UPDATE user SET id_etat_activite = '.$userState.' WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    public static function logout($pseudo) {
        setUserState($pseudo, 0);
        UserModel::deleteUserSession();
    }

    public static function deleteUserSession() {
        //session_unset();
        //session_destroy();
        unset($_COOKIE['PLUME_pseudo']);
    }

    public static function getUserState($pseudo) {
        $bdd = Database::connexionBDD();
        $req_id = $bdd->prepare('SELECT id_etat_activite FROM user WHERE pseudo = "'.$pseudo.'"');
        $req_id->execute();
        $idUser = $req_id->fetch(PDO::FETCH_ASSOC);

        $result = $idUser['id_etat_activite'];
        return $result;
    }

    /*Mettre à jour le nom de l'utilisateur*/
    public static function updateUserLastname($pseudo, $userLastname){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET nom = "'.$userLastname.'" WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Mettre à jour le prénom de l'utilisateur*/
    public static function updateUserFirstName($pseudo, $userFirstname){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET prenom = "'.$userFirstname.'" WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Mettre à jour le mot de passe de l'utilisateur*/
    public static function updateUserPassword($pseudo, $userPassword){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET password = '.MD5($userPassword).' WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Mettre à jour le mail de l'utilisateur*/
    public static function updateUserMail($pseudo, $userMail){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET email = '.$userMail.' WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Mettre à jour l'avatar de l'utilisateur*/
    public static function updateUserAvatar($pseudo, $userAvatar){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET avatar = '.$userAvatar.' WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Mettre à jour la ville de l'utilisateur*/
    public static function updateUserCity($pseudo, $userCity){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET ville = "'.$userCity.'" WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Mettre à jour la ville de l'utilisateur*/
    public static function updateUserColor($pseudo, $userColor){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET couleur = "'.$userColor.'" WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Mettre à jour l'avatar de l'utilisateur*/
    public static function updateUserAge($pseudo, $userAge){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET age = '.$userAge.' WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Mettre à jour la description de l'utilisateur*/
    public static function updateUserDescription($pseudo, $userDescription){
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('UPDATE user SET description = '.$userDescription.' WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Ajouter un hobby à l'utilisateur*/
    public static function setUserHobbies($pseudo, $nameHobby){
        $bdd = Database::connexionBDD();
        $idUser = UserModel::getUserId($pseudo);

        $req_idHobby = $bdd->prepare('SELECT ID FROM centre_interet WHERE Nom = "'.$nameHobby.'"');
        $req_idHobby->execute();
        $id_Hobby = $req_idHobby->fetch(PDO::FETCH_ASSOC);

        $req_active = $bdd->prepare('INSERT INTO user_centre_interet (ID, id_interet, id_user) VALUES (NULL,'.$id_Hobby['ID'].','.$idUser.')');
        $req_active->execute();
    }

    /*Supprimer un hobby à l'utilisateur*/
    public static function deleteUserHobbies($pseudo, $nameHobby){
        $bdd = Database::connexionBDD();
        $idUser = UserModel::getUserId($pseudo);

        $req_idHobby = $bdd->prepare('SELECT ID FROM centre_interet WHERE Nom = "'.$nameHobby.'"');
        $req_idHobby->execute();
        $id_Hobby = $req_idHobby->fetch(PDO::FETCH_ASSOC);

        $req_active = $bdd->prepare('DELETE FROM user_centre_interet WHERE id_interet = '.$id_Hobby['ID'].' AND id_user = '.$idUser);
        $req_active->execute();
    }

    /*Ajouter une langue à l'utilisateur*/
    public static function setUserLang($pseudo, $nameLang, $master){
        $bdd = Database::connexionBDD();
        $idUser = UserModel::getUserId($pseudo);

        $req_idLang = $bdd->prepare('SELECT ID FROM langue WHERE Nom = "'.$nameLang.'"');
        $req_idLang->execute();
        $id_lang = $req_idLang->fetch(PDO::FETCH_ASSOC);

        $req_active = $bdd->prepare('INSERT INTO user_langue (ID, id_user, id_langue, maitrise) VALUES (NULL,'.$idUser.','.$id_lang['ID'].','.$master.')');
        $req_active->execute();
    }

    /*Enlever une langue à l'utilisateur*/
    public static function deleteUserLang($pseudo, $nameLang){
        $bdd = Database::connexionBDD();
        $idUser = UserModel::getUserId($pseudo);

        $req_idLang = $bdd->prepare('SELECT ID FROM langue WHERE Nom = "'.$nameLang.'"');
        $req_idLang->execute();
        $id_lang = $req_idLang->fetch(PDO::FETCH_ASSOC);

        $req_active = $bdd->prepare('DELETE FROM user_langue WHERE id_langue = '.$id_lang['ID'].' AND id_user = '.$idUser);
        $req_active->execute();
    }

    /*Modifier le pays de l'utilisateur*/
    public static function updateUserPays($pseudo, $namePays){
        $bdd = Database::connexionBDD();
        $idUser = UserModel::getUserId($pseudo);

        $req_idPays = $bdd->prepare('SELECT id_pays FROM table_pays WHERE nom_pays = "'.$namePays.'"');
        $req_idPays->execute();
        $id_pays = $req_idPays->fetch(PDO::FETCH_ASSOC);

        $req_active = $bdd->prepare('UPDATE user SET id_pays = '.$id_pays['id_pays'].' WHERE pseudo = "'.$pseudo.'"');
        $req_active->execute();
    }

    /*Recherche par pseudo*/
    public static function userResearch($searched) { /*La variable $searched est ce que l'utilisateur a entré dans le champ de recherche*/
        $bdd = Database::connexionBDD();
        /* On cherche tous les pseudos contenant la suite de caractères entrée */
        $searchResults = $bdd->prepare('SELECT pseudo FROM user WHERE pseudo LIKE "%'.$searched.'%"');
        $searchResults->execute();
        $result=$searchResults->fetchAll();

        if($result==NULL){
            $result = array("Error", "Error: We can't find what you searched");
        }
        
        return $result;
    }

    /* Recherche/filtre pour match */
    public static function filterResearch($filterData){ /* tableau json contenant les filtres choisis par le user */
    	/* Tableau de la forme [ageMin,ageMax,sexe]
        La fonction filtrant les hobbies et langues est faite par Adrian */
    	$bdd = Database::connexionBDD();
    	$filterResults = $bdd->prepare('SELECT pseudo FROM user WHERE age >= "'.$filterData['ageMin'].'" AND age <= "'.$filterData['ageMax'].'" AND sexe ="'.$filterData['sexe'].'"');
    	$filterResults->execute();

    	if($searchResults->fetch()){
    		$result = $filterResults->fetch();
    	}
    	else{
    		$result = array("Error", "Error: We can't find anybody with this caracteristics ! Please change your filter.");
    	}

        $result=$filterResults->fetchAll();
    	if($result==NULL){
            $result = array("Error", "Error: We can't find anybody with this caracteristics ! Please change your filter.");
        }
    	return $result;

    }


    /*Récupère les langues maitrisées (2) par un utilisateur*/
    public static function getUserLangueMaitrisee($pseudo) {
        $bdd = Database::connexionBDD(); 

        $idUser = UserModel::getUserId($pseudo);

        $result= array('spokenLang' => array());

        if($idUser == NULL) return $result = array(0);
        else {
            $req_id = $bdd->prepare('SELECT DISTINCT id_langue, langue.Nom FROM user, user_langue, langue WHERE user_langue.maitrise = 2 AND user.ID=user_langue.id_user AND user.ID='.$idUser.' AND langue.ID = user_langue.id_langue');
            $req_id->execute();
            while($idLangueMaitrisee = $req_id->fetch(PDO::FETCH_ASSOC)){
                array_push($result['spokenLang'],$idLangueMaitrisee['Nom']);
            }
        }

        return $result;
    }

    /*Récupère les langues à apprendre (1) par un utilisateur*/
    public static function getUserLangueAApprendre($pseudo) {
        $bdd = Database::connexionBDD();  
        
        $idUser = UserModel::getUserId($pseudo);

        $result= array('learningLang' => array());

        if($idUser == NULL) return $result = array(0);
        else {
            $req_id = $bdd->prepare('SELECT DISTINCT id_langue, langue.Nom FROM user, user_langue, langue WHERE user_langue.maitrise = 1 AND user.ID=user_langue.id_user AND user.ID='.$idUser.' AND langue.ID = user_langue.id_langue');
            $req_id->execute();
            while($idLangueAApprendre = $req_id->fetch(PDO::FETCH_ASSOC)){
                array_push($result['learningLang'], $idLangueAApprendre['Nom']);

            }
        }
        return $result;
    }

    /*Récupère les possibles "Maitres" pour une langue donnée*/
    public static function findMaitre($pseudo, $idLangue) {
        $bdd = Database::connexionBDD(); 

        $idUser = UserModel::getUserId($pseudo);

        $req_id = $bdd->prepare('SELECT DISTINCT pseudo FROM user, user_langue, langue WHERE user_langue.maitrise=2 AND user_langue.id_langue='.$idLangue.' AND user.ID=user_langue.id_user AND user_langue.id_user !='.$idUser.' AND langue.ID = user_langue.id_langue');
        $req_id->execute();

        $result= array('maitres' => array());
        while($maitres = $req_id->fetch(PDO::FETCH_ASSOC)){
            $result['maitres'][] = $maitres['pseudo'];
        }
        return $result;
    }

    /*Récupère les possibles "Apprentis" pour une langue donnée*/
    public static function findApprenti($pseudo, $idLangue) {
        $bdd = Database::connexionBDD(); 

        $idUser = UserModel::getUserId($pseudo);
        $req_id = $bdd->prepare('SELECT DISTINCT pseudo FROM user, user_langue, langue WHERE user_langue.maitrise=1 AND user_langue.id_langue='.$idLangue.' AND user.ID=user_langue.id_user AND user_langue.id_user !='.$idUser.' AND langue.ID = user_langue.id_langue');
        $req_id->execute();

        $result= array('apprentis' => array());       
        while($apprentis = $req_id->fetch(PDO::FETCH_ASSOC)){
            $result['apprentis'][] = $apprentis['pseudo'];
        }

        return $result;
    }

    /*Récupère les centres d'interet d'un utilisateur*/
    public static function getUserCentreInteret($pseudo) {
        $bdd = Database::connexionBDD();  
        
        $idUser = UserModel::getUserId($pseudo);
        $req_id = $bdd->prepare('SELECT DISTINCT id_interet FROM user, user_centre_interet, centre_interet WHERE user_centre_interet.id_user ='.$idUser.' AND centre_interet.ID = centre_interet.ID AND user.ID = user_centre_interet.id_user');
        $req_id->execute();

        $result= array('hobbies' => array());
        while($idUserCentreInteret = $req_id->fetch(PDO::FETCH_ASSOC)){
            $result['hobbies'][] = $idUserCentreInteret["id_interet"];
        }

        return $result;
    }

    /*Filtre les utilisateurs ayant des matchs de langues et de centres d'interet*/
    public static function getUserMatch($pseudo, $role) {
        $requete = "SELECT DISTINCT user.ID, user_centre_interet.id_interet FROM user, user_centre_interet, user_langue WHERE user_centre_interet.id_interet = :id_interet AND user_centre_interet.id_user != :id_user AND user_langue.id_langue = :id_langue AND user_langue.maitrise= :role AND user.ID=user_langue.id_user AND user_centre_interet.id_user=user.ID";
        $bdd = Database::connexionBDD();

        $idUser = UserModel::getUserId($pseudo);

        if(isset($role)){
            if($role == "1") {
                $idLangues = UserModel::getUserLangueAApprendre($pseudo);
                $idLangues = $idLangues['learningLang'];
                $matchRole = 2;
            }
            else {
                $idLangues = UserModel::getUserLangueMaitrisee($pseudo);
                $idLangues = $idLangues['spokenLang'];
                $matchRole = 1;
            }
            $imaxLangues = count($idLangues);
        }


        
        $idCentreInteret = UserModel::getUserCentreInteret($pseudo);
        $imaxCentreInteret = count($idCentreInteret['hobbies']);

        $result= array('langue' => array("id_langue" => array(), "name_langue" => array(), "users" =>array() ));

        if(isset($idCentreInteret)){           
            for($j = 0; $j < $imaxLangues; $j++){
                $arret=0;
                $i =0;
                $result['langue']['id_langue'][$j] = $idLangues[$j]['id_langue'];
                $result['langue']['name_langue'][$j] = $idLangues[$j]['name_langue'];
                do {
                    $req_id = $bdd->prepare($requete);
                    $req_id->execute(array(':id_user' => $idUser, ':id_interet' => $idCentreInteret['hobbies'][$i], ':id_langue' => $idLangues[$j]['id_langue'], ':role' => $matchRole));

                    if($req_id!= NULL){
                        while($idUserMatched = $req_id->fetch(PDO::FETCH_ASSOC)){
                            $result['langue']['users'][$j][] = array("id_user" => $idUserMatched['ID'], "id_interet" => $idUserMatched['id_interet']);
                            $arret++;
                        }
                    }
                    ++$i;
                }while(($i < $imaxCentreInteret) && ($arret < 10));
            }
            $result = UserModel::ClasseUserMatch($result['langue']);
        }
        else $result = array(0);

        return $result;
    }

    /*Classe les utilisateurs ayant des matchs de langues et de centres d'interet, par tranche d'age et sexe défini*/
    public static function getUserMatchComplete($pseudo, $role, $gender, $agemin, $agemax) {
        $bdd = Database::connexionBDD();

        $idUser = UserModel::getUserId($pseudo);
         if(isset($role)){ 
            if($role == "1") { //Cherche des langues pour trouver des maitres
                $idLangues = UserModel::getUserLangueAApprendre($pseudo);
                $idLangues = $idLangues['learningLang'];
            }
            else { //Cherche des langues pour trouver des apprentis
                $idLangues = UserModel::getUserLangueMaitrisee($pseudo);
                $idLangues = $idLangues['spokenLang'];
            }
            $imaxLangues = count($idLangues);
        }

        $idCentreInteret = UserModel::getUserCentreInteret($pseudo);
        $imax = count($idCentreInteret['hobbies']);

        $result= array();
        $i =0;



        var_dump($idCentreInteret);

        if(isset($idCentreInteret)){
            $arret=0;
            echo('test');
            do {
                $req_id = $bdd->prepare('SELECT DISTINCT id_user, id_interet FROM user, user_centre_interet, centre_interet WHERE user_centre_interet.id_interet ='.$idCentreInteret['hobbies'][$i].' AND user_centre_interet.id_user != '.$idUser.' AND user.sexe ='.$gender.' AND user.age >='.$minOld.' AND user.age <='.$maxOld.' AND user.id_etat_activite = 1');
                $req_id->execute();

                while($idUserCentreInteret = $req_id->fetch(PDO::FETCH_ASSOC)){
                    $result['user_same_interet'][] = array("id_user" => $idUserCentreInteret['id_user'], "id_interet" => $idUserCentreInteret['id_interet']);
                    $arret++;
                    echo 'test';
                }
                ++$i;
            }while(($i < $imax-1) && ($arret < 10));
        }
        else $result = array(0);

        return $result;
    }

    /* Récupère un tableau d'utilisateur et les tris selon le nombre de centres d'interet en commun*/
    public static function ClasseUserMatch($listUserMatched){
        if(isset($listUserMatched)){
            $iMax = count ($listUserMatched['id_langue']); //Nombre d'utilisateur pour cette langue
            $temp = array('users' => array());


            for($i = 0; $i < $iMax; $i++){
                $jMaxUser = count($listUserMatched['users'][$i]); //Nombre d'utilisateur pour la langue i


                for($j = 0; $j < $jMaxUser; $j++){
                    $kMax = count($temp['users']); //Nombre d'utilisateur déjà triés pour la langue i

                    for ($k = 0; ($k < $kMax) || ($k == 0); $k++){
                        if(($kMax != 0) && ($listUserMatched['users'][$i][$j]['id_user'] == $temp['users'][$k]["id_user"])){
                            $temp['users'][$k]["nbCommuns"] += 1;
                            //var_dump($listUserMatched['users'][$i][$j]['id_interet']);
                            $temp['users'][$k]["id_interet"][] = $listUserMatched['users'][$i][$j]['id_interet'];
                        }
                        else {
                            $temp['users'][] = array('id_user' => $listUserMatched['users'][$i][$j]["id_user"], 'nbCommuns' => 1, 'id_interet' => array($listUserMatched['users'][$i][$j]['id_interet']), "infos" => userModel::getUser(userModel::getPseudoById($listUserMatched['users'][$i][$j]["id_user"])));
                            //var_dump($temp);
                        }
                    }
                 }     
                 $listUserMatched['users'][$i] = $temp['users'];                       
                $temp = array('users' => array()); //Réinitialisation du tableau temporaire
            }
        }

        return $listUserMatched;
    }/**/


    
    /* Création du profil (première connexion) */
    public static function setUserProfil($nom, $prenom, $pseudo, $email, $password, $avatar, $age, $sexe, $ville, $couleur, $date_inscription, $last_connection, $description, $pays, $id_etat_activ, $arr_hobbies, $arr_languesSpoken, $arr_languesLearning) {
        $bdd = Database::connexionBDD();

        /* Recherche si le pseudo existe :*/
        $req_pseudo = $bdd->prepare('SELECT ID FROM user WHERE pseudo = "'.$pseudo.'"');
        $req_pseudo->execute();
        
        if($donnees = $req_pseudo->fetch(PDO::FETCH_ASSOC)){
            $result = array("Error", "Error: Pseudo already taken");
        }
        else {
            /* Recherche si le mail deja used :*/
            $req_mail = $bdd->prepare('SELECT email FROM user WHERE pseudo = "'.$pseudo.'"');
            $req_mail->execute();
            if($donnees = $req_mail->fetch(PDO::FETCH_ASSOC)){
                $result = array("Error", "Error: mail already taken.");
            }
            else{
                $req_idPays = $bdd->prepare('SELECT id_pays FROM table_pays WHERE nom_pays = "'.$pays.'"');
                $req_idPays->execute();
                $id_pays = $req_idPays->fetch(PDO::FETCH_ASSOC);
                
                $password_hach = md5($password);

                /* Enregistrement de infos dans table users*/
                $req_user = $bdd->prepare('INSERT INTO user (nom, prenom, pseudo, email, password, avatar, age, sexe, ville, couleur,  date_inscription, derniere_connexion, description, id_pays, id_etat_activite) VALUES ("'.$nom.'", "'.$prenom.'", "'.$pseudo.'", "'.$email.'", "'.$password_hach.'", "'.$avatar.'", '.$age.', '.$sexe.', "'.$ville.'", "'.$couleur.'", '.$date_inscription.', '.$last_connection.', "'.$description.'", '.$id_pays['id_pays'].', '.$id_etat_activ.')');
                $req_user->execute(); 
                
                /* Enregistrement des hobbies */
                foreach($arr_hobbies as &$value){
                    $search_name_hobbies = $bdd->prepare('SELECT ID FROM centre_interet WHERE nom = "'.$value.'"');
                    $search_name_hobbies->execute();
                    
                    $id_user = UserModel::getUserId($pseudo);
                    
                    $id_hobbie = $search_name_hobbies->fetch(PDO::FETCH_ASSOC)['ID'];

                    $insere_hobbie = $bdd->prepare('INSERT INTO user_centre_interet(ID, id_user, id_interet) VALUES (NULL,'.$id_user.','.$id_hobbie.')');
                    $insere_hobbie->execute();
                }
                
                /* Enregistrement des Langues */
                
                /* Langues maitrisées */
                foreach($arr_languesSpoken as &$value){
                    $search_id_langue = $bdd->prepare('SELECT ID FROM langue WHERE Nom = "'.$value.'"');
                    $search_id_langue->execute();
                    
                    $id_langue = $search_id_langue->fetch(PDO::FETCH_ASSOC);
                    
                    $insere_langue = $bdd->prepare('INSERT INTO user_langue(ID, id_user, id_langue, maitrise) VALUES (NULL ,'.$id_user.','.$id_langue['ID'].', 2)');
                    $insere_langue->execute();
                }
                
                /* Langues à apprendre */
                foreach($arr_languesLearning as &$value){
                    $search_id_langue = $bdd->prepare('SELECT ID FROM langue WHERE Nom = "'.$value.'"');
                    $search_id_langue->execute(); 
                
                    $id_langue = $search_id_langue->fetch(PDO::FETCH_ASSOC);
                    
                    $insere_langue = $bdd->prepare('INSERT INTO user_langue(ID, id_user, id_langue, maitrise) VALUES (NULL ,'.$id_user.','.$id_langue['ID'].', 1)');
                    $insere_langue->execute();
                }

                $result = array(0);
            }
        }
        return $result;
    }
    
    public static function getUserId($pseudo) {
        $bdd = Database::connexionBDD();

        $req_active = $bdd->prepare('SELECT ID FROM user WHERE `pseudo` = "'.$pseudo.'"');
        $req_active->execute();

        if($id_user = $req_active->fetch(PDO::FETCH_ASSOC)){  
            $result = $id_user['ID'];
        }
        else $result = 0;
        
        return $result;
    }
    
    public static function getUserName($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT prenom FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_name = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = $user_name['prenom'];
        }
        else $result = array(0);
        
        return $result;
    }
    
    public static function getUserLastname($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT nom FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_lastname = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = $user_lastname['nom'];
        }
        else $result = array(0);
        
        return $result;  
    }
    
    public static function getUserDescription($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT description FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_desc = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = $user_desc['description'];
        }
        else $result = array(0);
        
        return $result;      
    }

    public static function getUserAvatar($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT avatar FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_avatar = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = $user_avatar['avatar'];
        }
        else $result = array(0);
        
        return $result;  
    }

    public static function getUserAge($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT age FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_age = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = $user_age['age'];
        }
        else $result = array(0);
        
        return $result;  
    }

    public static function getUserSex($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT sexe FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_sex = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = $user_sex['sexe'];
        }
        else $result = array(0);
        
        return $result;  
    }

    public static function getUserColor($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT couleur FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_color = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = $user_color['couleur'];
        }
        else $result = array(0);
        
        return $result;  
    }

    public static function getUserCity($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT ville FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_city = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = $user_city['ville'];
        }
        else $result = array(0);
        
        return $result;  
    }
    
    public static function getHobbies($id_hobbies) {
        $bdd = Database::connexionBDD();
        
        $req_active = $bdd->prepare('SELECT Nom FROM centre_interet WHERE ID = '.$id_hobbies);
        $req_active->execute();
        
        $hobbies = $req_active->fetch(PDO::FETCH_ASSOC);
        $result = $hobbies['Nom'];
        
        return $result;
    }
    
    public static function getUserHobbies($pseudo) {
        $bdd = Database::connexionBDD();
        $user_id = UserModel::getUserId($pseudo);
        $donnees = array();
        $i = 0;
        
        /* Recuperer les id des hobbies du user */
        $req_active = $bdd->prepare('SELECT id_interet FROM user_centre_interet WHERE id_user = '.$user_id);
        $req_active->execute();
        
        /*$result = $req_active->fetch(PDO::FETCH_ASSOC);*/
        /*$id_hobbies = array($result['id_interet']);*/
        
        /* Renvoyer les noms des hobbies du user */
        while($result = $req_active->fetch(PDO::FETCH_ASSOC)){
            $hobbies = UserModel::getHobbies($result['id_interet']);
            $donnees[$i] = $hobbies;
            $i++;
            }
        return $donnees;
    }
    
    public static function getUserDateInscription($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT date_inscription FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_inscription = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = array($user_inscription['date_inscription']);
        }
        else $result = array(0);
        
        return $result;        
    }
    
    public static function getUserMail($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT email FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_mail = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = array($user_mail['email']);
        }
        else $result = array(0);
        
        return $result;   
    }
    
    public static function getUserLastConnexion($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT derniere_connexion FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_lastConnect = $req_active->fetch(PDO::FETCH_ASSOC);
            $result = array($user_lastConnect['derniere_connexion']);
        }
        else $result = array(0);
        
        return $result;  
    }
    
    public static function getUserPays($pseudo) {
        $bdd = Database::connexionBDD();
        $id = UserModel::getUserId($pseudo);
        
        /* Recupere l'ID du pays dans la table user */
        if($id !== 0){
            $req_active = $bdd->prepare('SELECT id_pays FROM user WHERE ID ='.$id);
            $req_active->execute();
            $user_pays = $req_active->fetch(PDO::FETCH_ASSOC);
            $idPays = $user_pays['id_pays'];
            
            /* Recuperation du nom dans la table Pays */
            $req_nomPays = $bdd->prepare('SELECT nom_pays FROM table_pays WHERE id_pays = '.$idPays);
            $req_nomPays->execute();
            $nom_pays = $req_nomPays->fetch(PDO::FETCH_ASSOC);
            $result = $nom_pays['nom_pays'];
        }
        else $result = array(0);

        return $result;  
    }

    public static function getUser($pseudo){
        $data = array();
        
        $data["pseudo"] = $pseudo;
        $data["avatar"] = "";
        $data["name"] = UserModel::getUserLastName($pseudo);        
        $data["firstname"] = UserModel::getUserName($pseudo);
        $data["age"] = UserModel::getUserAge($pseudo);
        $data["sex"] = UserModel::getUserSex($pseudo);
        $data["description"] = UserModel::getUserDescription($pseudo);
        $data["town"] = UserModel::getUserCity($pseudo);
        $data["country"] = UserModel::getUserPays($pseudo);
        $date["couleur"] = UserModel::getUserColor($pseudo);
        $data["ville"] = UserModel::getUserCity($pseudo);
        $data["pays"] = UserModel::getUserPays($pseudo);
        $data["state"] = UserModel::getUserState($pseudo);
        $data["hobbies"] = UserModel::getUserHobbies($pseudo);
        $data["languages"][] = UserModel::getUserLangueMaitrisee($pseudo);
        $data["languages"][] = UserModel::getUserLangueAApprendre($pseudo);

        return $data;
    }
}

?>
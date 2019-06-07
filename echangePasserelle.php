<?php

    try {
        $bdd = new PDO('mysql:host=localhost;dbname=integration', 'root', '');
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
    function getCapteur($bdd,$id,$type){
        $query='select * from capteur join record on capteur.idCapteur=record.idCapteur where capteur.nCapteur='.$id.' and type="'.$type.'"';
        //echo $query;
        $ans=$bdd->query($query);
        $donnees = $ans->fetchall();
        //echo json_encode($donnees);
        return $donnees;
    }

    function getLastInsertId($bdd){
        $query='select LAST_INSERT_ID();';      // récupère le dernier id créé
        $ans=$bdd->query($query);
        $donnees = $ans->fetchall();
        $query='select * from capteur where idCapteur='.$donnees[0]['LAST_INSERT_ID()'];
        $ans=$bdd->query($query);
        $donnees = $ans->fetchall();
        return $donnees[0]['idCapteur'];
    }

/*     function updateValue($bdd,$nCapteur,$valeur){
        for ($i=0; $i<9 ; $i++) { 
            $query='update `record` SET `nRecord`='.($i+1)' WHERE nCapteur='.$nCapteur;
            $ans=$bdd->query($query);
        }
        $query='update `record` SET `valeur`='.$valeur' WHERE nCapteur='.$nCapteur;
        $ans=$bdd->query($query);
    } */

    function create_entry($bdd,$nCapteur,$type,$value){
        $data=getCapteur($bdd,$nCapteur,$type);
        //Si les capteurs n'ont jamais été vus les ajoutes dans la bdd
        if(sizeof($data)==0){
            $sql='insert into capteur (idCapteur,nCapteur,type) VALUES (?,?,?)';
            $stmt=$bdd->prepare($sql);
            if(!$stmt->execute([null,$nCapteur,$type])){
                echo $stmt->errorCode().'</br>';
                return;
            }
            $idCapteur=getLastInsertId($bdd);
            for ($i=0; $i < 10; $i++) {
                $sql='insert into record (idRecord,nRecord,idCapteur,valeur) VALUES (?,?,?,?)';
                $stmt=$bdd->prepare($sql);
                if(!$stmt->execute([null,$i,$idCapteur,(float)$value])){
                    echo $stmt->errorCode().'</br>';
                    return;
                }
            }
        // s'ils existent déjà met à jour les valeurs
        }else{
            $capteur= getCapteur($bdd,$nCapteur,$type);
            for ($i=1; $i < 10 ; $i++) { 
                $sql='update `record` SET `nRecord`='.($i+1).' WHERE idCapteur='.$capteur[0]['idCapteur'].' and nCapteur='.$i;
                //echo "update_".$i." ".$sql;
                $ans=$bdd->query($sql);
            }
            $sql='update `record` SET `valeur`='.$value.' WHERE idCapteur='.$capteur[0]['idCapteur'].' and nCapteur=0';
            //echo "update_0: ".$sql;
            $ans=$bdd->query($sql);
        }
    }

?>
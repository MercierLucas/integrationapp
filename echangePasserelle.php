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
        //print_r($data);
        if(sizeof($data)==0){
            $sql='insert into capteur (idCapteur,nCapteur,type) VALUES (?,?,?)';
            $stmt=$bdd->prepare($sql);
            if(!$stmt->execute([null,$nCapteur,$type])){
                echo $stmt->errorCode();
                return;
            }
            $idCapteur=getLastInsertId($bdd);
            for ($i=0; $i < 10; $i++) {
                echo $i.' ';
                $sql='insert into record (idRecord,nRecord,idCapteur,valeur) VALUES (?,?,?,?)';
                $stmt=$bdd->prepare($sql);
                if(!$stmt->execute([null,$i,$idCapteur,$value])){
                    echo $stmt->errorCode();
                    return;
                }
            }
        }
    }

?>
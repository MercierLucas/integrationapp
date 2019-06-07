<?php
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=integration', 'root', '');
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
    
    function getCapteurAJAX($bdd,$id,$type){
        $query='select * from capteur join record on capteur.idCapteur=record.idCapteur where capteur.nCapteur='.$id.' and type="'.$type.'"';
        //echo $query;
        $ans=$bdd->query($query);
        $donnees = $ans->fetchall();
        echo json_encode($donnees);
        //return $donnees;
    }

    getCapteurAJAX($bdd,$_POST['id'],$_POST['type']);

?>
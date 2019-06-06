<?php
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=integration', 'root', '');
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
    
    function getCapteur($bdd,$type){
        $query='select nCapteur from capteur where type ="'.$type.'" group by nCapteur';
        //echo $query;
        $ans=$bdd->query($query);
        $donnees = $ans->fetchall();
        echo json_encode($donnees);
        //return $donnees;
    }

    getCapteur($bdd,$_POST['type']);

?>
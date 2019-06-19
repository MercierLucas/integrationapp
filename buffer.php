<?php


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    
    <?php

        try {
            $bdd = new PDO('mysql:host=localhost;dbname=integration', 'root', '');
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
        $reset=false;
        if($reset){
            for ($i=0; $i < 9 ; $i++) { 
                $sql='update `record` SET `valeur`='.($i*10).' WHERE idCapteur=2 and nRecord='.($i+1);
                echo $sql."</br>";
                $stmt=$bdd->prepare($sql);
                if(!$stmt->execute()){
                    echo "ERREUR AVEC: ".$sql."</br>";
                    return;
                }
            }
        }else{
            $query='select * from record where idCapteur=2';
            $ans=$bdd->query($query);
            $records = $ans->fetchall();

            //print_r($records);
            for ($i=0; $i < 9 ; $i++) { 
                $sql='update `record` SET `valeur`='.$records[$i]['valeur'].' WHERE idCapteur=2 and nRecord='.($i+1);
                //echo $records[$i]['valeur']."</br>";
                echo $sql."</br>";
                $stmt=$bdd->prepare($sql);
                if(!$stmt->execute()){
                    echo "ERREUR AVEC: ".$sql."</br>";
                    return;
                }
            }
            if(isset($_POST['value'])){
                $sql='update `record` SET `valeur`='.(float)$_POST['value'].' WHERE idCapteur=2 and nRecord=0';
                //$sql='update `buffer` SET `valeur`=20 WHERE nCapteur=1';
                $stmt=$bdd->prepare($sql);
                if(!$stmt->execute()){
                    echo "update_0: ".$sql."</br>";
                    return;
                }
            }

        }

        

        $query='select * from record where idCapteur=2';
        //echo $query;
        $ans=$bdd->query($query);
        $donnees = $ans->fetchall();
        for ($i=0; $i < sizeof($donnees) ; $i++) { 
            echo $i.' : '.$donnees[$i]['valeur'].'</br>';
        }
        
        

    ?>
    <form action="" method="post">
        <input type="number" name="value">
        <input type="submit" value="envoyer">
    </form>

    <?php
        $query='select * from record where idCapteur=2';
        //echo $query;
        $ans=$bdd->query($query);
        $donnees = $ans->fetchall();
        for ($i=0; $i < sizeof($donnees) ; $i++) { 
            echo $i.' : '.$donnees[$i]['valeur'].'</br>';
        }
    
    ?>
<!--     <canvas id="myChart"></canvas>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'line',
            // The data for our dataset
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'My First dataset',
                    backgroundColor: 'rgb(255, 99, 132)',
                    borderColor: 'rgb(255, 99, 132)',
                    data: [0, 10, 5, 2, 20, 30, 45]
                }]
            },

            // Configuration options go here
            options: {}
        });
    </script> -->
</body>
</html>
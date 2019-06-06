<!DOCTYPE html>
<html>
<head>
	<title>Test intégration</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
</head>
<body>
<?php

    include('./echangePasserelle.php');

    $ch = curl_init();
        curl_setopt(
        $ch,
        CURLOPT_URL,
        "http://projets-tomcat.isep.fr:8080/appService/?ACTION=GETLOG&TEAM=009D");
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);


    //print_r(curl_getinfo($ch));
    $data = curl_exec($ch);
    if(curl_errno($ch)){
        echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);
    echo "Raw Data:<br />";
    echo gettype($data);
    //echo "<div style='background-color:red'>".$data."</div>";
    if(strlen($data)==0)
        echo "Pas de données à récupérer.";
    else 
        echo strlen($data);

    $data_tab = str_split($data,33);
    $dic = array(1 => "Distance",3 => "Temperature", 4 => "Humidite",5 => "Luminosite");
    echo "Tabular Data:<br />";
    echo "<table><thead><tr><th>Capteur</th><th>Numero</th><th>Valeur</th><th>Date</th></tr></thead>";
    for($i=0, $size=10; $i<$size; $i++){
        $trame = $data_tab[sizeof($data_tab)-($i+2)];     
        $t = substr($trame,0,1); //1
        $o = substr($trame,1,4); //4
        $r = substr($trame,5,1); //1
        $c = substr($trame,6,1); //1
        $n = substr($trame,7,2); //2
        $v = substr($trame,9,4); //4
        $a = substr($trame,13,4); //4
        $x = substr($trame,17,2); //2
        $year = substr($trame,19,4); //4
        $month = substr($trame,23,2); //2
        $day = substr($trame,25,2); //2
        $hour = substr($trame,27,2); //2
        $min = substr($trame,29,2); //2
        $sec = substr($trame,31,2); //2
        create_entry($bdd,$n,$dic[$c],hexdec($v));

/*         $trame = $data_tab[$i];
        // décodage avec des substring
        $t = substr($trame,0,1);
        $o = substr($trame,1,4);
        // …
        // décodage avec sscanf
        list($t, $o, $r, $c, $n, $v, $a, $x, $year, $month, $day, $hour, $min, $sec) = sscanf($trame,"%1s%4s%1s%1s%2s%4s%4s%2s%4s%2s%2s%2s%2s%2s"); */
        echo "<tr><th>".$dic[$c]."</th><th>".$n."</th><th>".hexdec($v)."</th><th>".$day."/".$month."/".$year." - ".$hour.":".$min.":".$sec."</th></tr>";
        //echo("<br />$t,$o,$r,$c,$n,$v,$a,$x,$year,$month,$day,$hour,$min,$sec<br />");
    }
    echo "</table>";

    //updateValue($)


/*     $donnees=getCapteur($bdd,31,"Temperature");
    for ($i=0; $i < sizeOf($donnees); $i++) { 
        echo $donnees[$i]['valeur'].'</br>';
    } */
    

?>

<style>

    body,html{
        margin:0;
        padding:0;
        width:100%;
    }
    thead{
        background-color:blue;
        color:#fff;
    }
    table{
        border-collapse:collapse;
        width:60%;
        margin-left:auto;
        margin-right:auto;
        height:300px;
        overflow-y:scroll;
    }

    th{
        border:1px solid black;
    }

    canvas{
        margin-left:auto;
        margin-right:auto;
    }
    
    #formSelectCapteur{
        margin-top:20px;
        margin-bottom:20px;
        width:60%;
        margin-left:auto;
        margin-right:auto;
        display:flex;
    }
    select{
        width:50%;
    }

    #loading{
        width:100px;
        margin-left:auto;
        margin-right:auto;
    }
</style>


<form action="" id="formSelectCapteur">
    <select name="" id="typeCapteur">
    </select>
    <select name="" id="nCapteur">
    </select>
</form>
<img src="loading.gif" alt="" id="loading" style="display:none;">
<canvas id="myCanvas" width="1000" height="300" />

<script src="ajax.js"></script>
</body>
</html>

<?php 

try {
        $bdd = new PDO('mysql:host=localhost;dbname=integration', 'root', '');
    } catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }

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

    $data_tab = str_split($data,33);
    $dic = array(1 => "Distance",3 => "Temperature", 4 => "Humidite",5 => "Luminosite");
    
    for ($i=0; $i < 3; $i++) { 
        
        $trame = $data_tab[sizeof($data_tab)-(2+$i)];     
        $t = substr($trame,0,1); //1
        $o = substr($trame,1,4); //4
        $r = substr($trame,5,1); //1
        $c = substr($trame,6,1); //1
        $n = substr($trame,7,2); //2
        $v = substr($trame,9,4); //4
        if($c==3) $v=hexdec($v)*0.588/10;
        else if($c==1){
            if($v==='0000') $v=0;
            else $v=1;
        }
        $a = substr($trame,13,4); //4
        $x = substr($trame,17,2); //2
        $year = substr($trame,19,4); //4
        $month = substr($trame,23,2); //2
        $day = substr($trame,25,2); //2
        $hour = substr($trame,27,2); //2
        $min = substr($trame,29,2); //2
        $sec = substr($trame,31,2); //2
        //create_entry($bdd,$n,$dic[$c],$v);
        $res[$i]="<tr><th>".$dic[$c]."</th><th>".$n."</th><th>".$v."</th><th>".$day."/".$month."/".$year." - ".$hour.":".$min.":".$sec."</th></tr>";
    }
    for ($i=0; $i < sizeOf($res); $i++) { 
        echo $res[$i].'</br>';
    }
    //echo json_encode($res);
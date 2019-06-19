<!DOCTYPE html>
<html>
<head>
	<title>Test intégration</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
</head>
<body>
    <div class="container">
        <table>
            <thead><tr><th>Capteur</th><th>Numero</th><th>Valeur</th><th>Date</th></tr></thead>
            <tbody id="servTable"></tbody>
        </table>
        <canvas id="myChart"></canvas>
    </div>


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
    #myChart{
        position:relative;
        width:40% !important;
    }
    .container{
        display:flex;
    }

    fieldset{
        width:30%;
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

<fieldset>
    <legend>Envoi d'ordre</legend>
    <form action="">
        <label for="">Température: </label>
        <input type="number" name="temperature" id="tempvalue">
        <input type="submit" value="Envoyer" id="temperature">
    </form>
    <form action="">
        <label for="">Température: </label>
        <input type="number" name="temperature" id="tempvalue">
        <input type="submit" value="Envoyer" id="temperature">
    </form>
</fieldset>
<!-- <canvas id="myCanvas" width="1000" height="300" /> -->


<script src="ajax.js"></script>
</body>
</html>

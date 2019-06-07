<!DOCTYPE html>
<html>
<head>
	<title>Test intégration</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
</head>
<body>
    <table>
        <thead><tr><th>Capteur</th><th>Numero</th><th>Valeur</th><th>Date</th></tr></thead>
        <tbody id="servTable"></tbody>
    </table>

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

var getHttpRequest= function(){
    // permet de supporter tous les navigateurs ( même les moins bons tels que IE .......)
    var httpRequest = false;
    if (window.XMLHttpRequest) { // Mozilla, Safari,...
        httpRequest = new XMLHttpRequest();
        if (httpRequest.overrideMimeType) {   // permet d'éviter un bug
          httpRequest.overrideMimeType('text/xml');
        }
      }
      else if (window.ActiveXObject) { // IE
        try {
          httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch (e) {
          try {
            httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
          }
          catch (e) {}
        }
      }
      
      if (!httpRequest) {
        alert('Abandon :( Impossible de créer une instance XMLHTTP');
        return false;
      }
    return httpRequest;
}


function getData(){
  console.log('Data status: Collecting...');
  var httpRequest=getHttpRequest();
  httpRequest.open('POST','./getDataFromServer.php',true);
  httpRequest.send();
  httpRequest.onreadystatechange=function(){
    if(httpRequest.readyState===4){
      document.getElementById('servTable').innerHTML="";
      // console.log("SERVER: "+httpRequest.responseText);
      obj=JSON.parse(httpRequest.responseText);
      //$('#servTable').append(obj[0]);
      //console.log("SERVER: "+obj[0]);
      for (let i = 0; i < obj.length; i++) {
        $('#servTable').append(obj[i]); 
        console.log('obj_'+i+ ' '+obj[i]);     
      }
      console.log('Data status: Done.'); 
    }
  }
}

function sendQuerry(type,src){
  var httpRequest=getHttpRequest();
  // 1 009D 1 3 01 002B 0125 1B
  var value=$('#'+src).val();
  switch(value.length){
    case 0:
      value='0000';
      break;
    case 1:
      value='000'+value;
      break;
    case 2:
      value='00'+value;
      break;
    case 3:
      value='0'+value;
      break;
    case 4:
        break;
    default:
      value="0000";
      break;
  }
  var trame='1009D2'+type+'01'+value+'01251B';
  //var core=

  var url="http://projets-tomcat.isep.fr:8080/appService/?ACTION=COMMAND&TEAM=009d&TRAME="+trame;
  httpRequest.open('GET',url,true);
  httpRequest.send();
  httpRequest.onreadystatechange=function(){
    if(httpRequest.readyState===4){
      console.log(trame);
    }
  }
}
function listNumCapteur(){
    var type=$('#typeCapteur').val();
    var httpRequest=getHttpRequest();
    httpRequest.open('POST','./getNCapteur.php',true);
    var data=new FormData();
    data.append('type',type);
    httpRequest.send(data);
    httpRequest.onreadystatechange=function(){
      if(httpRequest.readyState===4){
        document.getElementById('nCapteur').innerHTML="";
        console.log(httpRequest.responseText);
        obj=JSON.parse(httpRequest.responseText);
        for (let i = 0; i < obj.length; i++) {
            if(i==0) $('#nCapteur').append("<option value="+obj[i].nCapteur+" selected>"+obj[i].nCapteur+"</option>");
            else $('#nCapteur').append("<option value="+obj[i].nCapteur+" >"+obj[i].nCapteur+"</option>");
            
        }
      }
    }
}
function listTypeCapteur(){
    var httpRequest=getHttpRequest();
    httpRequest.open('POST','./getExistingCapteur.php',true);
    var data=new FormData();
    data.append('groupBY','type');
    httpRequest.send(data);
    httpRequest.onreadystatechange=function(){
      if(httpRequest.readyState===4){
        console.log(httpRequest.responseText);
        obj=JSON.parse(httpRequest.responseText);
        for (let i = 0; i < obj.length; i++) {
            if(i==0) $('#typeCapteur').append("<option value="+obj[i].type+" selected>"+obj[i].type+"</option>");
            else $('#typeCapteur').append("<option value="+obj[i].type+" >"+obj[i].type+"</option>");
        }
        $("#typeCapteur").val($("#typeCapteur").children()[0].text);  
  
      }
    }
}
function loading(state){
    if(state){
        document.getElementById('loading').style.display="block";
        document.getElementById('myCanvas').style.display="none";
    }else{
        document.getElementById('myCanvas').style.display="block";
        document.getElementById('loading').style.display="none";
    }
}
function update(){
/*     var id=idUser.replace('rowitem_','');
    console.log(id); */
    var type=$('#typeCapteur').val();
    var id=$('#nCapteur').val();
    var httpRequest=getHttpRequest();
    httpRequest.open('POST','./getCapteur.php',true);
    var data=new FormData();
    data.append('id',id);
    data.append('type',type);
    httpRequest.send(data);
    //loading(true);
    httpRequest.onreadystatechange=function(){
      if(httpRequest.readyState===4){
        //console.log(httpRequest.responseText);
        obj=JSON.parse(httpRequest.responseText);
        for (let i = 0; i < obj.length; i++) {
          console.log('obj_'+i+ ' '+obj[i].valeur+' '+obj[i].type);     
        }
        chartjs(obj);
        //updateGraph(obj,type); 
/*          setTimeout(function(){ 
            updateGraph(obj,type);
            loading(false)},1000); */
      } 
    }
  }

  $(document).on('change','#nCapteur',function(e) {
      update();
  });

  $('#temperature').click(function(e){
    e.preventDefault();
    sendQuerry(3,'tempvalue');
  });

  $(document).on('change','#typeCapteur',function(e) {
    listNumCapteur();
    update();
});

  $(window).on('load',function(e){
    update();
    listNumCapteur();
    listTypeCapteur();
    setInterval(function(){getData();update();},6000);
}); 


function chartjs(obj){
  var ctx = document.getElementById('myChart').getContext('2d');
  var label=[];
  var data=[];
  for (let i = 0; i < obj.length; i++) {
    label.push(i);
    data.push(obj[obj.length-i-1].valeur);
  }
  var chart = new Chart(ctx, {
      // The type of chart we want to create
      type: 'line',
      
      // The data for our dataset
      data: {
          labels: label,
          datasets: [{
              label: 'My First dataset',
              fill:false,
              backgroundColor: 'rgb(255, 99, 132)',
              borderColor: 'rgb(255, 99, 132)',
              data: data
          }]
      },

      // Configuration options go here
      options: {
        maintainAspectRatio: false,
        responsive: false,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        },
        animation: {
          duration: 0
        }
      }
  });
}
function updateGraph(obj,titre){
    var canvas = document.getElementById('myCanvas');
    var ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);   // reset canvas
    
    
    var yTitle =titre;


    var grid_size = 10;
    var x_axis_distance_grid_lines = 29;
    var y_axis_distance_grid_lines = 1;
    var x_axis_starting_point = { number: 1, suffix: '\u03a0' };
    var y_axis_starting_point = { number: 1, suffix: '' };


    // canvas width
    var canvas_width = canvas.width;

    // canvas height
    var canvas_height = canvas.height;

    // no of vertical grid lines
    var num_lines_x = Math.floor(canvas_height/grid_size);

    // no of horizontal grid lines
    var num_lines_y = Math.floor(canvas_width/grid_size);

    // axe des abscisses
    // Draw grid lines along X-axis
    for(var i=0; i<=num_lines_x; i++) {
        ctx.beginPath();
        ctx.lineWidth = 1;
        
        // If line represents X-axis draw in different color
        if(i == x_axis_distance_grid_lines) 
            ctx.strokeStyle = "#000000";
        else
            ctx.strokeStyle = "#e9e9e9";
        
        if(i == num_lines_x) {
            ctx.moveTo(0, grid_size*i);
            ctx.lineTo(canvas_width, grid_size*i);
        }
        else {
            ctx.moveTo(0, grid_size*i+0.5);
            ctx.lineTo(canvas_width, grid_size*i+0.5);
        }
        // on met les abscisses

        ctx.stroke();
    }

    // axe des ordonnes
    // Draw grid lines along Y-axis
    for(i=0; i<=num_lines_y; i++) {
        ctx.beginPath();
        ctx.lineWidth = 1;
        
        // If line represents Y-axis draw in different color
        if(i == y_axis_distance_grid_lines) 
            ctx.strokeStyle = "#073b4c";
        else
            ctx.strokeStyle = "#e9e9e9";
        
        if(i == num_lines_y) {
            ctx.moveTo(grid_size*i, 0);
            ctx.lineTo(grid_size*i, canvas_height);
        }
        else {
            ctx.moveTo(grid_size*i+0.5, 0);
            ctx.lineTo(grid_size*i+0.5, canvas_height);
        }
        ctx.stroke();
    }

    for(i=0; i<=canvas.height; i+=10) {
        ctx.fillText((canvas.height-i)/10, 10, i);
    }

    for(i=0; i<=canvas.width; i+=100) {

        ctx.fillText(i/100, i+25, canvas.height-10);

    }

/*     for (let i=0; i<=30;i++){
        yValues.push(Math.floor(Math.random()*(250-0+1)+0));
    } */


    var width = 15;
    var currX = 20;
    var base = 200;

    // x: 1carreau = 15px
    // y: 1 carreau = 25px

    ctx.beginPath();
    for (var i=0;i<obj.length;i++) {
        var h = parseFloat(obj[obj.length-i-1].valeur);
        var y = canvas.height - h*10;
        console.log('i:'+i+' v:'+h);
        //Lines
        ctx.strokeStyle = '#ff7a99';
        ctx.lineWidth = 2;
        if (i == 0) {
            ctx.moveTo(currX + width /2, y);
        }
        else {
            ctx.fillText(h.toFixed(2), currX + width /2, y-10);
            ctx.lineTo(currX + width /2, y);
        }
        
        currX += width + 85;
    }

    ctx.stroke();

    ctx.fillText(yTitle, 30, 10);
  }
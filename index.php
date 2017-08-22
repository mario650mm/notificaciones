<?php 
  $update = isset($_GET['update']) ? $_GET['update']:0;

  $tituloPost = isset($_POST['titulo']) ? $_POST['titulo']:0;

  $cuerpoPost = isset($_POST['cuerpo']) ? $_POST['cuerpo']:0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"/>
	<title>Firebase database</title>
</head>
<body>
<div class="container">
	<h3 style="color:#2F98BF;">Notificaciones</h3>
	<br>
  <div id="alertSuccess" class="alert alert-success" style="display:none;"></div>
  <br>
	<div class="row">
    <div class="col-sm-6">
      <label for="titulo">Título</label>
      <input type="text" id="titulo" name="titulo" class="form-control" maxlength="100" />
    </div>
		<div class="col-sm-6">
			<label for="cuerpo">Cuerpo</label>
			<textarea type="text" id="cuerpo" name="cuerpo" class="form-control" rows="6" cols="80"></textarea> 
		</div>
	</div>
  <div class="row">
      <div class="col-sm-3" style="margin-top:30px;">
      <button class="btn btn-success" onclick="return save(event);">
      Registrar</button>
    </div>
  </div>
	<br><br> 
  <h3 align="center" style="color:#0DA6C5;">Notificaciones</h3>
	<table id="tablaNotificaciones" class="table table-hover table-responsive" 
  style="display:block;">
    <thead id="divAcciones">    
    </thead>
    <tbody id="divEjercicios"></tbody>
	</table>
</div>
<input type="hidden" id="validacionHidden" name="validacionHidden" />
<input type="hidden" id="roleHidden" name="roleHidden" />
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<!--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.1.1/firebase.js"></script>
<script>
var res = "<?php echo $res; ?>";
var validacion = null;
var role = '';
var imgMain = '&nbsp;&nbsp;<img src="icon.png" width="50px" height="50px" />';
var valorUpdate ="<?php echo $update; ?>";
window.onload = function(){
   acceso();
   setTimeout(function(){
      llenarTabla();
      if(valorUpdate != 0){
        console.log("valor update "+valorUpdate);
        mostrarNotificacionById(valorUpdate);
      }
   },800);
};
  var config = {
    apiKey: "AIzaSyC19RF5MhVJhlrrN9LZ3SRpBrqjwWztyiw",
    authDomain: "notificacion-cd32e.firebaseapp.com",
    databaseURL: "https://notificacion-cd32e.firebaseio.com/",
    storageBucket: "notificacion-cd32e.appspot.com",
    messagingSenderId: "665323947746",
  };
  firebase.initializeApp(config);

  function acceso(){
    var authentication = firebase.auth();
    var correo = 'vzert@gmail.com';
    var promise = authentication.signInWithEmailAndPassword(correo,'$vzertwifi$');
    firebase.auth().onAuthStateChanged(function(user){
        if(user){
          var usuario = correo.replace('@hotmail.com','');
          usuario = usuario.replace('@gmail.com','');
          usuario = usuario.replace('@yahoo.com','');
          referencia = firebase.database().ref('Notificaciones/users/'+usuario);
          referencia.once('value').then(function(data){
              if(data.key == usuario){
                  document.getElementById('validacionHidden').value = 1;
                  var strRole = JSON.stringify(data.val().roles);
                  if(strRole == '{"lector":true}'){
                      document.getElementById('roleHidden').value = 2;
                  }else{
                    document.getElementById('roleHidden').value = 1;
                  }
              }else{
                document.getElementById('validacionHidden').value = 0;
              }
          });
        }else{
          document.getElementById('validacionHidden').value = 0;
        }
    });
  }

  function llenarTabla(){
        baseReferencia = firebase.database().ref('Notificaciones/notifications/');
        baseReferencia.once('value').then(function(data) {
        var lista = '';
        var encabezados = '';
          data.forEach(function(child){ 
            if(data.key != child.key){
                baseReferencia2 = firebase.database().ref('Notificaciones/notifications/'+child.key+'/');
                if(document.getElementById('roleHidden').value == 1){
                      encabezados = '<tr><th width="350px">Titulo</th><th width="800px">Cuerpo</th><th width="200px">Acciones</th></tr>';
                  lista += '<tr><td>'+child.val()["titulo"]+'</td><td>'+child.val()["cuerpo"]+'</td><td><a href="edit.php?id='+child.key+'" class="btn btn-primary btn-sm">Editar</a></td></tr>';
                }else{
                    encabezados = '<tr><th width="350px">Titulo</th><th width="800px">Cuerpo</th></tr>';
                  lista += '<tr><td>'+child.val()["titulo"]+'</td><td>'+child.val()["cuerpo"]+'</td></tr>';
                }         
            }                           
          });
          lista += '</tr>';
          document.getElementById('divAcciones').innerHTML = encabezados;
          document.getElementById('divEjercicios').innerHTML = lista;
        }); 
  } 

  function mostrarNotificacion(){
    baseReferencia = firebase.database().ref('/Notificaciones/notifications/').orderByChild('/Notificaciones/notifications/').limitToLast(1);
      baseReferencia.once('value').then(function(data) {
        data.forEach(function(child){ 
          //var Noti = JSON.stringify(child.val());
          if(data.key != child.key){
              baseReferencia2 = firebase.database().ref('Notificaciones/notifications/'+child.key+'/');
          }
          //----------------------------NOTIFICACION (API MOZILLA)-------------------
      if(!("Notification" in window)){
          alert("Este Navegador no permite notificaciones");
      }
      else if(Notification.permission == "granted"){
          /*var options ={
            body: 'Frecuencia ' + child.val()["Frecuencia"],
            icon:"icon.png",
            sound:"tono_loco.mp3"
          }
          //var notification = new Notification('Ejercicio '+  child.val()["Ejercicio"],options);
          //notification.sound;
          //setTimeout(notification.close.bind(notification), 4000); */
          toastr.options = {
            "closeButton": true,
            "timeOut": "0",
            "extendedTimeOut": "0",
            "preventDuplicates":true
          };
          toastr.success(child.val()["titulo"]+imgMain+'</br>'+child.val()["cuerpo"]);
    } else if(Notification.permission !== 'denegado'){
         Notification.requestPermission(function(permission){

          if(!('permission') in Notification){
            Notification.permission = permission;
          }
         });
      }                         
      });
    });
  }


  function mostrarNotificacionById(idN){
    baseReferencia = firebase.database().ref('/Notificaciones/notifications/'+idN+'/');
      var cuerpo = '';
      var cont = 0;
      baseReferencia.once('value').then(function(data) {
        data.forEach(function(child){ 
         
          baseReferencia2 = firebase.database().ref('Notificaciones/notifications/'+child.key+'/');
          //----------------------------NOTIFICACION (API MOZILLA)-------------------
      if(!("Notification" in window)){
          alert("Este Navegador no permite notificaciones");
      }
      else if(Notification.permission == "granted"){
          toastr.options = {
            "closeButton": true,
            "timeOut": "0",
            "extendedTimeOut": "0",
            "preventDuplicates":true
          };
          if(cont == 0){
            cuerpo = child.val()+imgMain;
          }else{
            toastr.success(child.val()+'<br>'+cuerpo);
          }
          cont++;
    } else if(Notification.permission !== 'denegado'){
         Notification.requestPermission(function(permission){

          if(!('permission') in Notification){
            Notification.permission = permission;
          }
         });
      }                         
      });
    });
  }
  
  function save(event){
  	event.preventDefault();
    if(document.getElementById('validacionHidden').value == 1 && 
      document.getElementById('roleHidden').value == 1){
       if(document.getElementById('titulo').value != '' && 
        document.getElementById('cuerpo').value != ''){
          var txtTitulo = document.getElementById('titulo').value;
          var txtCuerpo = document.getElementById('cuerpo').value;
          firebase.database().ref('Notificaciones/notifications/').push({
            titulo:txtTitulo,
            cuerpo:txtCuerpo
          });
          var xhttp = new XMLHttpRequest();
          xhttp.open("POST", "https://fcm.googleapis.com/fcm/send",true);
          xhttp.setRequestHeader("Content-Type", "application/json");
          xhttp.setRequestHeader("Authorization", "key=AIzaSyBJJiISyr3m1KERRywg-xgjmghqjc6dasA");
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              if(this.status == 200){
                console.log('notificación enviada correctamente');
              }
            }
          };
          xhttp.send(JSON.stringify(
          {"notification":{"body":txtCuerpo,"title":txtTitulo,"sound":"default","priority":"high"},"data":{"id":null},
                "to":"e-ueqv-RDe8:APA91bFL2VTLKszrsO8kV1WnYf4RsKKch5lR4pQc0trDFNz8v6bXAXT_-HZsC30xqRbr7I02LDe4WF_yUpM8PT27lYkE0MXzkTvUGtRoAZo0vNi7RVG8zJW43X4ToOY-QtzgL3NdccuL"
          }));
        setTimeout(function(){   
            $("#alertSuccess").html(document.getElementById('titulo').value+' registrado éxitosamente ');   
            $("#alertSuccess").show('fast');
        },200);
        setTimeout(function(){
          $("#alertSuccess").html('');
          $("#alertSuccess").hide('fast');
        },5000);
        document.getElementById('titulo').value = '';
        document.getElementById('cuerpo').value = '';
        llenarTabla();
        mostrarNotificacion();
      }else{
        alert('Por favor llene todos los campos');
      }
    }else{
      alert('Debes ser administrador para hacer esta operación');
    }
  }
</script>	
</body>
</html>
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
	<h3>Insertado valores</h3>
	<br>
  <div id="alertSuccess" class="alert alert-success" style="display:none;"></div>
  <br>
	<div class="row">
    <div class="col-sm-4">
      <label for="tipoNotificacion">Tipo de notificación</label>
      <select id="tipoNotificacion" name="tipoNotificacion" class="form-control">
      </select>
    </div>
		<div class="col-sm-4">
			<label for="tipo">Tipo de ejercicio</label>
			<input type="text" id="tipo" name="tipo" class="form-control" />
		</div>
		<div class="col-sm-4">
			<label for="frecuencia">Frecuencia</label>
			<select id="frecuencia" name="frecuencia" class="form-control">
        <option value="">seleccione una frecuencia</option>
        <option value="Diario">Diario</option>
        <option value="Semanal">Semanal</option>
        <option value="Mensual">Mensual</option>
      </select>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-sm-3">
			<button class="btn btn-success" onclick="return insertEjercicio(event,'tipoNotificacion');">
      Registrar</button>
		</div>
	</div>
	<br> 
  <div class="row">
    <div class="col-sm-4 pull-right">
      <label for="tipoNot">Tipo Notificación</label>
      <select id="tipoNot" name="tipoNot" class="form-control">
      </select>
    </div>
    <div class="col-sm-3" style="margin-top:35px;">
      <button class="btn btn-default btn-sm" onclick="llenarTabla('tipoNot');">Buscar</button>
    </div>
    <div class="col-sm-3" style="margin-top:35px;">
      <button class="btn btn-primary btn-sm" onclick="mostrarNotificaciones('tipoNot');">Mostrar notificaciones</button>
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
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.1.1/firebase.js"></script>
<script>
var res = "<?php echo $res; ?>";
var validacion = null;
var role = '';
window.onload = function(){
   acceso();
  setTimeout(function(){
      llenarCombo(document.getElementById('validacionHidden').value);
  },1000);
  /*setTimeout(function(){
      if(parseInt(document.getElementById('validacionHidden').value) == 1){
        $("#tablaNotificaciones").css('display','block');
    }else{
        $("#tablaNotificaciones").css('display','none');
    }
  },2200);*/
  
  
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

  function llenarTabla(tipoNot){
    if(document.getElementById('validacionHidden').value == 1){
        var tipo = document.getElementById(tipoNot).value;
        baseReferencia = firebase.database().ref('Notificaciones/'+tipo+'/Recomendaciones/');
        baseReferencia.once('value').then(function(data) {
        var lista = '';
        var encabezados = '';
          data.forEach(function(child){   
            if(data.key != child.key){
                baseReferencia2 = firebase.database().ref('Notificaciones/Correo/Recomendaciones/'+child.key+'/');
                if(document.getElementById('roleHidden').value == 1){
                      encabezados = '<tr><th width="100%">Tipo de ejercicio</th><th width="100%">Frecuencia</th><th width="100%">Acciones</th></tr>';
                  lista += '<tr><td>'+child.val()["Ejercicio"]+'</td><td>'+child.val()["Frecuencia"]+'</td><td><a href="edit.php?id='+child.key+'&tipo='+tipo+'" class="btn btn-primary btn-sm">Editar</a></td></tr>';
                }else{
                    encabezados = '<tr><th width="100%">Tipo de ejercicio</th><th width="100%">Frecuencia</th></tr>';
                  lista += '<tr><td>'+child.val()["Ejercicio"]+'</td><td>'+child.val()["Frecuencia"]+'</td></tr>';
                }          }                           
          });
          lista += '</tr>';
          document.getElementById('divAcciones').innerHTML = encabezados;
          document.getElementById('divEjercicios').innerHTML = lista;
        }); 
    }  
  } 

  function llenarCombo(validacion){
     if(parseInt(document.getElementById('validacionHidden').value) == 1){
        baseReferencia = firebase.database().ref('Notificaciones/');
        baseReferencia.once('value').then(function(data){
          var block = '<option value="">tipo de notificación</option>';
          data.forEach(function(child){
            if(data.key != child.key){
                block += '<option value="'+child.key+'">'+child.key+'</option>';
            }
          });
          document.getElementById('tipoNotificacion').innerHTML = block;
          document.getElementById('tipoNot').innerHTML = block;
        });
    }
  }

  function mostrarNotificaciones(tipoNot){
      var tipo = document.getElementById(tipoNot).value;
      if(tipo != ''){
           baseReferencia = firebase.database().ref('/Notificaciones/'+tipo+'/Recomendaciones/').orderByChild('/Notificaciones/'+tipo+'/Recomendaciones/').limitToLast(5);
        baseReferencia.once('value').then(function(data) {
        //var lista = '';
        data.forEach(function(child){ 
          //var Noti = JSON.stringify(child.val());
          /*if(data.key != child.key){
              baseReferencia2 = firebase.database().ref('Notificaciones/Correo/Recomendaciones/'+child.key+'/');
              console.log(child.val()["Ejercicio"]);
              console.log(child.val()["Frecuencia"]);
          }  */
          //----------------------------NOTIFICACION (API MOZILLA)-------------------
      if(!("Notification" in window)){
          alert("Este Navegador no permite notificaciones");
      }
      else if(Notification.permission == "granted"){
          //var titulo = child.val()["Ejercicio"];
          var options ={
            body: 'Frecuencia ' + child.val()["Frecuencia"],
            icon:"icon.png",
            sound:"tono_loco.mp3"
          };
          //console.log(options);
          var notification = new Notification('Ejercicio '+  child.val()["Ejercicio"],options);
          notification.sound;
      } 
      else if(Notification.permission !== 'denegado'){
         Notification.requestPermission(function(permission){

          if(!('permission') in Notification){
            console.log('permiso denegado');
            Notification.permission = permission;
          }
         });
      }                         
    });
    });
      }
  }
  
  function insertEjercicio(event,tipoN){
  	event.preventDefault();
    console.log(document.getElementById('roleHidden').value);
    if(document.getElementById('validacionHidden').value == 1 && 
      document.getElementById('roleHidden').value == 1){
       if(document.getElementById('tipoNotificacion').value != '' && 
        document.getElementById('tipo').value != '' && 
        document.getElementById('frecuencia').value != ''){
          firebase.database().ref('Notificaciones/'+document.getElementById(tipoN).value+'/Recomendaciones/').push({
            Ejercicio:document.getElementById('tipo').value,
            Frecuencia: document.getElementById('frecuencia').value
          });
        setTimeout(function(){
            $("#alertSuccess").html('Registro éxitoso de ejercicio '+document.getElementById('tipoNotificacion').value);
            $("#alertSuccess").show('fast');
        },200);
        setTimeout(function(){
          $("#alertSuccess").html('');
          $("#alertSuccess").hide('fast');
        },5000);
        document.getElementById('tipoNotificacion').value = '';
        document.getElementById('tipo').value = '';
        document.getElementById('frecuencia').value = '';
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
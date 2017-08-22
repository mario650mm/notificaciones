<?php
$id = isset($_GET['id']) ? $_GET['id']:0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"/>
	<title>Edición de una recomendación</title>
</head>
<body>
<div class="container">
	<h3>Edición</h3>
	<br>
	<div id="alertGeneral" class="alert" style="display:none;"></div>
	<div class="row">
		<div class="col-sm-6">
			<label for="titulo">Título</label>
			<input type="text" id="titulo" name="titulo" class="form-control" maxlength="100" />
		</div>
		<div class="col-sm-6">
			<label for="cuerpo">Cuerpo</label>
			<textarea id="cuerpo" name="cuerpo" class="form-control" cols="80" rows="6"></textarea>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-sm-3">
			<button class="btn btn-success btn-sm" onclick="return editarRegistro(event);">
			Actualizar</button>
		</div>
		<div class="col-sm-3">
			<button class="btn btn-danger btn-sm" onclick="return eliminarRegistro(event);">Eliminar</button>
		</div>
	</div>
</div>	
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.1.1/firebase.js"></script>
<script type="text/javascript">
	var id = "<?php echo $id; ?>";
	var tipoE = "<?php echo $tipo; ?>";
	window.onload = function(){
		traerDatos(id);
	};
  var config = {
    apiKey: "AIzaSyC19RF5MhVJhlrrN9LZ3SRpBrqjwWztyiw",
    authDomain: "notificacion-cd32e.firebaseapp.com",
    databaseURL: "https://notificacion-cd32e.firebaseio.com/",
    storageBucket: "gs://notificacion-cd32e.appspot.com",
    //messagingSenderId: "notificacion-cd32e",
  };
  firebase.initializeApp(config);

  function traerDatos(){
  	actividadReferencia = firebase.database().ref('Notificaciones/notifications/'+id+'/');
  	actividadReferencia.on('value',function(datos){
  		document.getElementById('titulo').value = datos.val().titulo;
  		document.getElementById('cuerpo').value = datos.val().cuerpo;
  	});

  }

  function editarRegistro(event,tipo){
      event.preventDefault();
      var confirmacion = confirm('Estás seguro de actualizar el registro '+id+' ?');
      if(confirmacion == true){
      	if(document.getElementById('titulo').value != '' && document.getElementById('cuerpo').value != ''){
      		var txtCuerpo = document.getElementById('cuerpo').value;
      		var txtTitulo = document.getElementById('titulo').value;
      		actividadReferencia = firebase.database().ref('Notificaciones/notifications/'+id+'/');
	  		actividadReferencia.update({
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
	  		$("#alertGeneral").addClass('alert-success');
	  		$("#alertGeneral").html('Registro actualizado correctamente');
	  		$("#alertGeneral").show('fast');
	  	},200);
	  	setTimeout(function(){
	  		$("#alertGeneral").removeClass('alert-success');
	  		$("#alertGeneral").html('');
	  		$("#alertGeneral").hide('fast');
	  	},4000);
	  	setTimeout(function(){
	  		window.location = 'index.php?update='+id;
	  	},600);
      	}else{
      		 alert('Por favor llene todos los campos');
      	}
      	
	  	
      }
  }

  function eliminarRegistro(event){
  	event.preventDefault();
  	var confirmacion = confirm('Estás seguro de eliminar este registro !');
  	if(confirmacion == true){
  		actividadReferencia = firebase.database().ref('Notificaciones/notifications/'+id+'/');
  		actividadReferencia.remove();
  		setTimeout(function(){
	  		$("#alertGeneral").addClass('alert-danger');
	  		$("#alertGeneral").html('Registro eliminado correctamente');
	  		$("#alertGeneral").show('fast');
	  	},200);
	  	setTimeout(function(){
	  		$("#alertGeneral").removeClass('alert-danger');
	  		$("#alertGeneral").html('');
	  		$("#alertGeneral").hide('fast');
	  	},4000);
	  	setTimeout(function(){
	  		window.location = 'index.php';
	  	},600);
  	}
  }

</script>
</body>
</html>
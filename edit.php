<?php
$id = isset($_GET['id']) ? $_GET['id']:0;
$tipo = isset($_GET['tipo']) ? $_GET['tipo']:0;
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
		<!--<div class="col-sm-4">
	      	<label for="tipoNotificacion">Tipo de notificación</label>
	      	<select id="tipoNotificacion" name="tipoNotificacion" class="form-control">
	      	</select>
    	</div>-->
		<div class="col-sm-4">
			<label for="tipo">Ejercicio</label>
			<input type="text" id="tipo" name="tipo" class="form-control" />
		</div>
		<div class="col-sm-4">
			<label for="frecuencia" id="lblEjercicio">Frecuencia</label>
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
			<button class="btn btn-success btn-sm" onclick="return editarRegistro(event,'ejercicio');">
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
		//llenarCombo();
		traerDatos(id);
		/*setTimeout(function(){
			document.getElementById('tipoNotificacion').value = tipoE;
		},600);*/
	};
  var config = {
    apiKey: "AIzaSyC19RF5MhVJhlrrN9LZ3SRpBrqjwWztyiw",
    authDomain: "notificacion-cd32e.firebaseapp.com",
    databaseURL: "https://notificacion-cd32e.firebaseio.com/",
    storageBucket: "gs://notificacion-cd32e.appspot.com",
    //messagingSenderId: "notificacion-cd32e",
  };
  firebase.initializeApp(config);

  function llenarCombo(){
    baseReferencia = firebase.database().ref('Notificaciones/');
    baseReferencia.once('value').then(function(data){
      var block = '<option value=""> tipo de notificación</option>';
      data.forEach(function(child){
          if(data.key != child.key){
              block += '<option value="'+child.key+'">'+child.key+'</option>';
          }
      });
      document.getElementById('tipoNotificacion').innerHTML = block;
    });
  }

  function traerDatos(tipo){
  	actividadReferencia = firebase.database().ref('Notificaciones/'+tipoE+'/Recomendaciones/'+id+'/');
  	actividadReferencia.on('value',function(datos){
  		//console.log(datos.val());
  		document.getElementById('tipo').value = datos.val().Ejercicio;
  		document.getElementById('frecuencia').value = datos.val().Frecuencia;
  	});

  }

  function editarRegistro(event,tipo){
      event.preventDefault();
      var confirmacion = confirm('Estás seguro de actualizar el registro '+id+' ?');
      if(confirmacion == true){
      	if(document.getElementById('tipo').value != '' && document.getElementById('frecuencia').value != ''){
      		actividadReferencia = firebase.database().ref('Notificaciones/'+tipoE+'/Recomendaciones/'+id+'/');
	  		actividadReferencia.update({
	  		Ejercicio:document.getElementById('tipo').value,
	  		Frecuencia:document.getElementById('frecuencia').value
	  	});
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
		  		window.location = 'index.php';
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
  		actividadReferencia = firebase.database().ref('Notificaciones/'+tipoE+'/Recomendaciones/'+id+'/');
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
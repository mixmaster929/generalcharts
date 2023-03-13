<?php 
include 'db_connect.php'; 
include 'constants.php'; 
$type = 0;
$userId = 0;
if(isset($_GET['type']) && isset($_GET['userId'])){
	$type = $_GET['type'];
	$userId = $_GET['userId'];
}
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM tenants where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
	$$k=$val;
}
}
?>
<div class="container-fluid">
	<form action="" id="manage-tenant">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="row form-group">
			<div class="col-md-4">
				<label for="" class="control-label">Nombre</label>
				<input <?php echo (isset($_GET['id']) && $userId != $partnerId && intval($type) == ID_TYPE_PARTNER) ? "disabled" : '' ?> type="text" class="form-control" name="lastName"  value="<?php echo isset($lastName) ? $lastName :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Apellido</label>
				<input <?php echo (isset($_GET['id']) &&  $userId != $partnerId && intval($type) == ID_TYPE_PARTNER) ? "disabled" : '' ?> type="text" class="form-control" name="firstName"  value="<?php echo isset($firstName) ? $firstName :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Segundo nombre</label>
				<input <?php echo (isset($_GET['id']) &&  $userId != $partnerId && intval($type) == ID_TYPE_PARTNER) ? "disabled" : '' ?> type="text" class="form-control" name="middleName"  value="<?php echo isset($middleName) ? $middleName :'' ?>">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Email</label>
				<input <?php echo (isset($_GET['id']) &&  $userId != $partnerId && intval($type) == ID_TYPE_PARTNER) ? "disabled" : '' ?> type="email" class="form-control" name="email"  value="<?php echo isset($email) ? $email :'' ?>" required>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Contacto #</label>
				<input <?php echo (isset($_GET['id']) &&  $userId != $partnerId && intval($type) == ID_TYPE_PARTNER) ? "disabled" : '' ?> type="text" class="form-control" name="contact"  value="<?php echo isset($contact) ? $contact :'' ?>" required>
			</div>
			<?php if(intval($type) !== ID_TYPE_PARTNER): ?>
				<div class="col-md-4">
				<label for="" class="control-label">Partner</label>
					<select name="partnerId" id="partnerId" class="custom-select select2">
						<option value="-1">Sin asignacion</option>
						<?php 
						$partners = $conn->query("SELECT id, name, username FROM users WHERE type = ".ID_TYPE_PARTNER."");
						while($row= $partners->fetch_assoc()):
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($partnerId) && $partnerId == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] . ' - ' . $row['username'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
			<?php endif; ?>

		</div>
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Patinete</label>
				<select style='width: 75% !important;' name="scooterId" id="scooterId" class="custom-select select2" required="required">
					<option value="">Selecionar...</option>
					<?php 
					$patinetes = $conn->query("SELECT s.price, s.id, s.scooterNumber, st.name as stateName, (SELECT count(*) FROM scooter_incidents as si WHERE si.scooterId = s.id AND si.resolved = 0) as incidents FROM scooters as s INNER JOIN states as st ON st.id = s.stateId WHERE 1 = 1" .(isset($scooterId) ? " AND s.id = $scooterId"." OR ": " AND " ). "  (st.name = '".STATE_FREE."'".(intval($type) == ID_TYPE_PARTNER ? " AND s.partnerId = ".$userId.")" : ")" ));
					while($row= $patinetes->fetch_assoc()):
					?>
					<option data-incidents="<?php echo $row['incidents'] ?>" data-scooterId="<?php echo $row['id'] ?>" data-price="<?php echo $row['price'] ?>" value="<?php echo $row['id'] ?>" <?php echo isset($scooterId) && $scooterId == $row['id'] ? 'selected' : '' ?> class="scooter_option_<?php echo($row['incidents']) > 0 ? strtolower(STATE_INCIDENT) : strtolower(STATE_FREE) ?>"><?php echo $row['scooterNumber'] ?></option>
					<?php endwhile; ?>
				</select>
				<span id='incidents' data-type="<?php echo($type) ?>"  data-scooterId='0' style='display:none;'><i class="fa fa-exclamation-triangle" aria-hidden="true" ></i><span id='totalIncidents'></span></span>
			</div>
			<div class="col-md-4">
				<label for="" class="control-label">Fecha de registración</label>
				<input <?php echo (isset($_GET['id']) &&  $userId != $partnerId && intval($type) == ID_TYPE_PARTNER) ? "disabled" : '' ?> type="date" class="form-control" name="dateIn" value="<?php echo isset($dateIn) ? date("Y-m-d",strtotime($dateIn)) :'' ?>" required>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-md-4">
				<label for="" class="control-label">Archivos:</label>
				<input <?php echo (isset($_GET['id']) && $userId != $partnerId && intval($type) == ID_TYPE_PARTNER) ? "disabled" : '' ?> name="upload[]" type="file" multiple="multiple" />
			</div>
		</div>
	</form>
</div>
<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md large" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Lista de incidencias</h5>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="incidents_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='submitIncident' onclick="$('#manage-incident').submit()">Guardar</button>
        <button type="button" class="btn btn-secondary" id='cancelIncidents' data-toggle="modal" data-target="#incidents_modal" >Cancelar</button>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
              <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
              <img src="" alt="">
      </div>
    </div>
  </div>
<script>
	$( document ).ready(function() {
		$('#scooterId').trigger("change");
    });
	
	$('#scooterId').change(function(){
		if($(this).val() <= 0)
			return false;
		const incidents = $(this).find('option:selected').attr('data-incidents');
		const scooterId = $(this).find('option:selected').attr('data-scooterId');

		if(incidents > 0){
			$("#incidents").show();
			$("#totalIncidents").text('('+incidents+')');
			$("#incidents").attr('data-scooterId', scooterId)
		}else{
			$("#incidents").hide();
		}
   	})

	$('#incidents').click(function(){
		start_load()
		const url = "view_incidents.php?scooterId="+$(this).attr('data-scooterId')+"&type="+$(this).attr('data-type');
		const type = $(this).attr('data-type');

		$.ajax({
			url,
			error:err=>{
				alert("An error occured")
			},
			success:function(resp){
				if(resp){
					let btnSave = true;
					if(type == 2){
						btnSave = false;
					}
					if(!btnSave){
						$('#submitIncident').hide();
						$('#cancelIncidents').text('Cerrar');
					}
					$('#incidents_modal .modal-body').html(resp)
					$('#incidents_modal').modal({
					show:true,
					backdrop:'static',
					keyboard:false,
					focus:true
					})
					end_load()
				}
			}
		})
	})


	$('#manage-tenant').submit(function(e){
		e.preventDefault();
		if(!$('#manage-tenant')[0].checkValidity()){
			$('#manage-tenant')[0].reportValidity()
			return;
		}
		start_load()
		let formData = new FormData($(this)[0]);
		formData.append('price', $("#scooterId option:selected" ).attr('data-price'));
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_tenant',
			data: formData,
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Se guardo el alquiler con exito.",'success')
						setTimeout(function(){
							location.reload()
						},1000)
				}
			}
		})
	})
</script>
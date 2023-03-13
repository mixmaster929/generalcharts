<?php 
include 'db_connect.php'; 
$scooterId = isset($_GET['scooterId']) ? $_GET['scooterId'] : '';
$scooterNumber = isset($_GET['scooterNumber']) ? $_GET['scooterNumber'] : '';
?>
<div class="container-fluid">
	<form action="" id="manage-incident">
		<input type="hidden" name="scooterId" value="<?php echo isset($scooterId) ? $scooterId : '' ?>">
		<div class="row form-group">
			<div class="col-md-1"></div>
				<label for="" class="control-label">Patinete #: <?php echo isset($scooterNumber) ? $scooterNumber : '' ?> </label>
			</div>
			<div class="col-md-12">
				<label for="" class="control-label">Descripcion</label>
                <textarea name="description" id="description" cols="30" rows="4" class="form-control" required></textarea>
            </div>
        </div>
	</form>
</div>
<script>
	
	$('#manage-incident').submit(function(e){
		e.preventDefault()
		if(!$('#manage-incident')[0].checkValidity()){
			$('#manage-incident')[0].reportValidity()
			return;
		}
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_incident',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Se guardo la incidencia con exito.",'success')
						setTimeout(function(){
							location.reload()
						},1000)
				}else{
                    alert_toast("Ocurrio un error al crear la incidencia.",'danger')
						setTimeout(function(){
							location.reload()
						},1000)
                }
			}
		})
	})
</script>
<?php include 'db_connect.php';
include 'constants.php'; ?>

<?php 
$type = 0;
if(isset($_GET['type'])){
	$type = $_GET['type'];
}
$scooterId = isset($_GET['scooterId']) ? $_GET['scooterId'] : '';
$incidents = $conn->query("SELECT si.description, si.resolved, si.id as incidentId, DATE_FORMAT(si.createdAd,'%m/%d/%Y %H:%i') as createdAd,  DATE_FORMAT(si.updatedAt,'%m/%d/%Y %H:%i') as updatedAt, s.scooterNumber FROM scooters as s INNER JOIN scooter_incidents AS si ON si.scooterId = s.id WHERE s.id = $scooterId");
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-12">
				<large><b>Lista de incidencias</b></large>
					<hr>
                <form action="" id="manage-incident">
                    <table class="table table-condensed table-striped">
                        <thead>
                            <tr>				
                                <th>Descripcion</th>
                                <th>Fecha de creacion</th>
                                <th>Fecha de actualizacion</th>
                                <th>Estado</th>
                                <?php if(intval($type) != ID_TYPE_PARTNER): ?>
                                <th></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if($incidents->num_rows > 0):
                            while($row=$incidents->fetch_assoc()):
                            ?>
                        <tr>
                            <td><?php echo $row['description'] ?></td>
                            <td><?php echo $row['createdAd'] ?></td>
                            <td><?php echo $row['updatedAt'] ?></td>
                            <td><?php echo($row['resolved'] ? 'Resuelta' : 'Sin resolver')?></td>
                            <?php if(intval($type) != ID_TYPE_PARTNER): ?>
                                <td>
                                    <input name="incidents[]" id="<?php echo $row['incidentId'] ?>" type="checkbox" <?php echo($row['resolved'] ? 'checked="checked"' : '')?> />
                                    <input type="hidden" id="old_resolved_<?php echo $row['incidentId'] ?>" name="old_resolved_<?php echo $row['incidentId'] ?>" value="<?php echo $row['resolved'] ?>" />
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </form>
			</div>
		</div>
	</div>
</div>
<style>
	#details p {
		margin: unset;
		padding: unset;
		line-height: 1.3em;
	}
	td, th{
		padding: 3px !important;
	}
</style>
<script>
	
	$('#manage-incident').submit(function(e){
		e.preventDefault(); 
		start_load();
		$('#msg').html('');
        let formData = new FormData();
        var checkbox = $("#manage-incident").find("input[type=checkbox]");
        let incidents = new Array();
        $.each(checkbox, function(key, val) {
            let isChecked = $(val).is(':checked') ? 1 : 0;
            let id = $(val).attr('id');
            if(parseInt(isChecked) !== parseInt($("#old_resolved_"+id).val())){
                let incident = {};
                incident.id = $(val).attr('id');
                incident.resolve = $(val).is(':checked') ? 1 : 0;
                incidents.push(incident);
            }
        });
        incidents = JSON.stringify(incidents);
        formData.append('incidents',incidents);
		$.ajax({
			url:'ajax.php?action=resolve_incident',
			data: formData,
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
				}else if(resp==2){
                    alert_toast("Ocurrio un error al crear la incidencia.",'danger')
						setTimeout(function(){
							location.reload()
						},1000)
                }else if(resp==3){
						setTimeout(function(){
							location.reload()
						},1000)
                }
			}
		})
	})

</script>
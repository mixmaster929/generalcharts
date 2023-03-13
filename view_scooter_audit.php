<?php include 'db_connect.php';
include 'constants.php'; ?>

<?php 
$scooterId = isset($_GET['scooterId']) ? $_GET['scooterId'] : '';
$histories = $conn->query("SELECT id, scooterId, action, oldObject, newObject, DATE_FORMAT(date,'%m/%d/%Y %H:%i') as date, username FROM scooter_audits WHERE scooterId = $scooterId");
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-12">
                    <table class="table table-condensed table-striped" id="scooter_audits">
                        <thead>
                            <tr>				
                                <th>Accion</th>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if($histories->num_rows > 0):
                            while($row=$histories->fetch_assoc()):
                            ?>
                        <tr>
                            <td><?php echo $row['action'] ?></td>
                            <td><?php echo $row['date'] ?></td>
                            <td><?php echo $row['username'] ?></td>
                            <td><button class="btn btn-sm btn-info view_audit" type="button" data-id="<?php echo $row['id'] ?>" data-scooterId="<?php echo $row['scooterId'] ?>">Ver</button></td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md mid-large" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Lista de cambios</h5>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal_scooter_audit" role='dialog'>
    <div class="modal-dialog modal-md mid-large" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title"></h5>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modal_scooter_audit" >Cancelar</button>
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
    $(document).ready(function(){
		$('#scooter_audits').dataTable({ pageLength: 5, bLengthChange: false})
	})

    $('.view_audit').click(function(){
		start_load()
		const url = "view_audit.php?id="+$(this).attr('data-id')+"&scooterId="+$(this).attr('data-scooterId');
		$.ajax({
			url,
			error:err=>{
				alert("An error occured")
			},
			success:function(resp){
				if(resp){
					$('#modal_scooter_audit .modal-body').html(resp)
					$('#modal_scooter_audit').modal({
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

</script>
<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
			<form action="" id="manage-state">
				<div class="card">
					<div class="card-header">
						    Tipo de estados
				  	</div>
					<div class="card-body">
							<input type="hidden" name="id">
							<div class="form-group">
								<label class="control-label">Nombre</label>
								<input type="text" class="form-control" name="name" required>
							</div>
							<div class="form-group">
								<label class="control-label">Descripcion</label>
								<input type="text" class="form-control" name="description" required>
							</div>
					</div>
							
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-sm btn-primary col-sm-4 offset-md-2"> Guardar</button>
								<button class="btn btn-sm btn-default col-sm-4" type="button" onclick="$('#manage-state').get(0).reset()"> Cancelar</button>
							</div>
						</div>
					</div>
				</div>
			</form>
			</div>
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<b>Lista de tipos de estados</b>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Nombre</th>
									<th class="text-center">Descripcion</th>
									<th class="text-center">Accion</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$states = $conn->query("SELECT * FROM states order by id asc");
								while($row=$states->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p><b><?php echo $row['name'] ?></b></p>
									</td>
									<td class="">
										<p><b><?php echo $row['description'] ?></b></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary edit_state" type="button" data-id="<?php echo $row['id'] ?>"  data-name="<?php echo $row['name'] ?>" data-description="<?php echo $row['description'] ?>">Editar</button>
										<button class="btn btn-sm btn-danger delete_state" type="button" data-id="<?php echo $row['id'] ?>">Eliminar</button>
									</td>
								</tr>
								<?php endwhile; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Table Panel -->
		</div>
	</div>	

</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
</style>
<script>
	
	$('#manage-state').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_state',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
                console.log(resp);
				if(resp==1){
					alert_toast("Ya existe un estado con ese nombre",'danger')
					setTimeout(function(){
						location.reload()
					},1500)
					
				}
				else if(resp==2){
					alert_toast("Se agrego el tipo de estado con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}else if(resp==3){
					alert_toast("Se actualizo el tipo de estado con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	})
	$('.edit_state').click(function(){
		start_load()
		var state = $('#manage-state')
		state.get(0).reset()
		state.find("[name='id']").val($(this).attr('data-id'))
		state.find("[name='name']").val($(this).attr('data-name'))
		state.find("[name='description']").val($(this).attr('data-description'))
		end_load()
	})
	$('.delete_state').click(function(){
		_conf("Esta seguro que desea eliminar el tipo de estado?","delete_state",[$(this).attr('data-id')])
	})
	function delete_state($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_state',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Se elimino con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}else{
					alert_toast("Ocurrio un error al eliminar el estado, existen patinetes asignados al mismo",'danger')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
	$( document ).ready(function() {
		$('table').dataTable({
			"columnDefs": [{
				"searchable": false,
				"targets": [3]
			}]
		}) 	
	});
</script>
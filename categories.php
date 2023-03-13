<?php include('db_connect.php');?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row">
			<!-- FORM Panel -->
			<div class="col-md-4">
			<form action="" id="manage-category">
				<div class="card">
					<div class="card-header">
						    Tipo de patinete
				  	</div>
					<div class="card-body">
							<input type="hidden" name="id">
							<div class="form-group">
								<label class="control-label">Nombre</label>
								<input type="text" class="form-control" name="name" required>
							</div>
							<div class="form-group">
								<label class="control-label">Precio</label>
								<input type="number" step="any" class="form-control" name="price" required>
							</div>
					</div>
							
					<div class="card-footer">
						<div class="row">
							<div class="col-md-12">
								<button class="btn btn-sm btn-primary col-sm-4 offset-md-2"> Guardar</button>
								<button class="btn btn-sm btn-default col-sm-4" type="button" onclick="$('#manage-category').get(0).reset()"> Cancelar</button>
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
						<b>Lista de tipos de patinetes</b>
					</div>
					<div class="card-body">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Nombre</th>
									<th class="text-center">Precio</th>
									<th class="text-center">Accion</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$category = $conn->query("SELECT * FROM categories order by id asc");
								while($row=$category->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p><b><?php echo $row['name'] ?></b></p>
									</td>
									<td class="">
										<p><b><?php echo $row['price'] ?></b></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-primary edit_category" type="button" data-id="<?php echo $row['id'] ?>"  data-name="<?php echo $row['name'] ?>" data-price="<?php echo $row['price'] ?>">Editar</button>
										<button class="btn btn-sm btn-danger delete_category" type="button" data-id="<?php echo $row['id'] ?>">Eliminar</button>
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
	
	$('#manage-category').submit(function(e){
		e.preventDefault()
		start_load()
		$.ajax({
			url:'ajax.php?action=save_category',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Se agrego el tipo de patinete con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
				else if(resp==2){
					alert_toast("Se edito el tipo de patinete con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	})
	$('.edit_category').click(function(){
		start_load()
		var cat = $('#manage-category')
		cat.get(0).reset()
		cat.find("[name='id']").val($(this).attr('data-id'))
		cat.find("[name='name']").val($(this).attr('data-name'))
		cat.find("[name='price']").val($(this).attr('data-price'))
		end_load()
	})
	$('.delete_category').click(function(){
		_conf("Esta seguro que desea eliminar el tipo de patinete?","delete_category",[$(this).attr('data-id')])
	})
	function delete_category($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_category',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Se elimino con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}else{
					alert_toast("Ocurrio un error al eliminar la categoria, existen patienes asignados a la misma",'danger')
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
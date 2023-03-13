<?php 
include('db_connect.php');

$stateId = isset($_GET['stateId']) && $_GET['stateId'] !== 0 ? $_GET['stateId'] : null;
?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row">
			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>Lista de patinetes</b>
						<span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="javascript:void(0)" id="new_scooter">
						<i class="fa fa-plus"></i> Nuevo patinete
						</a></span>
					</div>
					<div class="card-body">
						<form id="filter-scooter">
								<div class="row form-group">
									<label class="control-label col-md-3 offset-md-1 text-right">Estado: </label>	
									<select name="stateId" id="stateId" class="custom-select col-md-4" required>
										<option value="0">Todos</option>
										<option <?php echo($stateId == -1 ? 'selected' : '')  ?> value="-1">Incidencia</option>
										<?php 
											$states = $conn->query("SELECT name, id FROM states order by name asc");
											if($states->num_rows > 0):
												while($row= $states->fetch_assoc()) :
										?>
											<option <?php echo($stateId == $row['id'] ? 'selected' : '')  ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
										
										<?php endwhile; ?>
										<?php endif; ?>
									</select>
									<button class="btn btn-sm btn-block btn-primary col-md-2 ml-1">Filtrar</button>
								</div>
						</form>
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">Patinete</th>
									<th class="text-center">Accion</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$query = "SELECT s.*,c.name as cname, c.price as categoryPrice, st.name as stateName, (SELECT count(*) FROM scooter_incidents as si WHERE si.scooterId = s.id AND si.resolved = 0) as incidents, u.name as nameUser, (SELECT concat(lastName,', ',firstName,' ',middleName) FROM tenants WHERE scooterId = s.id AND status = 1 order by dateIn asc limit 1) as client FROM scooters s ";
								$query .= "inner join categories c on c.id = s.categoryId "; 
								$query .= "inner join states st on st.id = s.stateId " ;
								$query .= "left outer join users u on u.id = s.partnerId "; 
								$query .= " WHERE 1 = 1 ";
								$query .= $stateId  > 0 ? " AND s.stateId = $stateId " : " ";
								$query .= intval($_SESSION['login_type']) == ID_TYPE_PARTNER ? " AND s.partnerId = ".$_SESSION['login_id'] : "";
								$query .= $stateId == -1 ? " having incidents > 0 " : " ";
								$query .= " order by id asc ";
								$scooters = $conn->query($query);
								while($row=$scooters->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class="">
										<p>Patinete #: <b><?php echo $row['scooterNumber'] ?></b></p>
										<p><small>Tipo de patinete: <b><?php echo $row['cname'] ?></b></small></p>
										<p><small>Descripcion: <b><?php echo $row['description'] ?></b></small></p>
										<p>
											<small>
												Estado: <b><?php echo $row['stateName'] ?></b>
												<?php if($row['incidents'] > 0): ?>
												<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> (<?php echo $row['incidents'] ?>)
												<?php endif; ?>
											</small>
										</p>
										<p><small>Precio: <b><?php echo number_format($row['price'],2) ?></b></small></p>
										<p><small>Asignado a: <b><?php echo (isset($row['partnerId']) ? $row['nameUser'] : $row['client']) ?></b></small></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-warning new_incident" type="button" data-id="<?php echo $row['id'] ?>" data-scooterNumber="<?php echo $row['scooterNumber'] ?>">Incidencia</button>
										<button class="btn btn-sm btn-outline-primary view_incidents" data-type="<?php echo($_SESSION['login_type']) ?>" type="button" data-scooterId="<?php echo $row['id'] ?>" >Ver incidencias</button>
										<?php if(intval($_SESSION['login_type']) != ID_TYPE_PARTNER): ?>
											<button class="btn btn-sm btn-primary edit_scooter" type="button" data-partnerId="<?php echo ($row['partnerId'] ? $row['partnerId'] : 0) ?>" data-id="<?php echo $row['id'] ?>"  data-scooterNumber="<?php echo $row['scooterNumber'] ?>" data-description="<?php echo $row['description'] ?>" data-categoryId="<?php echo $row['categoryId'] ?>" data-price="<?php echo $row['price'] ?>" data-stateId="<?php echo $row['stateId'] ?>" 
											data-field1="<?php echo $row['field1'] ?>" 
											data-field2="<?php echo $row['field2'] ?>" 
											data-field3="<?php echo $row['field3'] ?>" 
											data-field4="<?php echo $row['field4'] ?>" 
											data-field5="<?php echo $row['field5'] ?>" 
											data-field6="<?php echo $row['field6'] ?>" 
											data-field7="<?php echo $row['field7'] ?>" 
											data-field8="<?php echo $row['field8'] ?>" 
											data-field9="<?php echo $row['field9'] ?>" 
											data-field10="<?php echo $row['field10'] ?>" 
											data-field11="<?php echo $row['field11'] ?>" 
											data-field12="<?php echo $row['field12'] ?>" 
											data-field13="<?php echo $row['field13'] ?>" 
											data-field14="<?php echo $row['field14'] ?>" 
											data-field15="<?php echo $row['field15'] ?>" 
											data-field16="<?php echo $row['field16'] ?>" 
											data-field17="<?php echo $row['field17'] ?>" 
											data-field18="<?php echo $row['field18'] ?>" 
											data-field19="<?php echo $row['field19'] ?>" 
											data-field20="<?php echo $row['field20'] ?>" 
											data-field21="<?php echo $row['field21'] ?>" 
											data-field22="<?php echo $row['field22'] ?>" 
											data-field23="<?php echo $row['field23'] ?>" 
											data-field24="<?php echo $row['field24'] ?>" 
											data-field25="<?php echo $row['field25'] ?>" 
											data-field26="<?php echo $row['field26'] ?>" 
											data-field27="<?php echo $row['field27'] ?>" 
											data-field28="<?php echo $row['field28'] ?>" 
											data-field29="<?php echo $row['field29'] ?>" 
											data-field30="<?php echo $row['field30'] ?>" 
											>Editar</button>
											<button class="btn btn-sm btn-danger delete_scooter" type="button" data-id="<?php echo $row['id'] ?>">Eliminar</button>
											<button class="btn btn-sm btn-info view_scooter_audit" type="button" data-scooterId="<?php echo $row['id'] ?>">Historial</button>

										<?php endif; ?>
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
<div class="modal fade" id="scooter_confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md large" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Patinete</h5>
      </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal_scooter" role='dialog'>
    <div class="modal-dialog modal-md large" role="document">
      <div class="modal-content">
        <div class="modal-header">
		<h5 class="modal-title">Patinete</h5>
      </div>
      <div class="modal-body">
		<!-- FORM Panel -->
		<?php if(intval($_SESSION['login_type']) != ID_TYPE_PARTNER): ?>
			<form action="" id="manage-scooter">
				<div class="form-group" id="msg"></div>
				<input type="hidden" name="id">
				<div class="row form-group">
					<div class="col-md-4">
						<label class="control-label">Patinete No</label>
						<input type="text" class="form-control" name="scooterNumber" required>
					</div>
					<div class="col-md-4">
						<label class="control-label">Tipo de patinete</label>
						<select name="categoryId" id="categoryId" class="custom-select" required>
							<option selected value="" disabled="">Seleccionar</option>
							<?php 
							$categories = $conn->query("SELECT * FROM categories order by name asc");
							if($categories->num_rows > 0):
							while($row= $categories->fetch_assoc()) :
							?>
							<option data-price="<?php echo $row['price'] ?>" value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
						<?php endwhile; ?>
						<?php endif; ?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="control-label">Estado</label>
						<select name="stateId" id="stateId" class="custom-select" required>
							<option selected value="" disabled="">Seleccionar</option>
							<?php 
							$states = $conn->query("SELECT name, id FROM states order by name asc");
							if($states->num_rows > 0):
							while($row= $states->fetch_assoc()) :
							?>
							<option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
						<?php endwhile; ?>
						<?php endif; ?>
						</select>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-4">
					<label for="" class="control-label">Partner</label>
						<select name="partnerId" id="partnerId" class="custom-select">
							<option value="0">Sin asignar</option>
							<?php 
							$partners = $conn->query("SELECT id, name, username FROM users WHERE type = ".ID_TYPE_PARTNER);
							while($row= $partners->fetch_assoc()):
							?>
							<option value="<?php echo $row['id'] ?>" <?php echo isset($partnerId) && $partnerId == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] . ' - ' . $row['username'] ?></option>
							<?php endwhile; ?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="control-label">Precio</label>
						<input type="number" class="form-control text-right" name="price" step="any" required="">
					</div>
					<div class="col-md-4">
						<label for="" class="control-label">Descripcion</label>
						<textarea name="description" id="" cols="30" rows="4" class="form-control"></textarea>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-4">
						<label class="control-label">Field 1</label>
						<input type="text" class="form-control text-right" name="field1">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 2</label>
						<input type="text" class="form-control text-right" name="field2">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 3</label>
						<input type="text" class="form-control text-right" name="field3">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 4</label>
						<input type="text" class="form-control text-right" name="field4">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 5</label>
						<input type="text" class="form-control text-right" name="field5">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 6</label>
						<input type="text" class="form-control text-right" name="field6">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 7</label>
						<input type="text" class="form-control text-right" name="field7">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 8</label>
						<input type="text" class="form-control text-right" name="field8">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 9</label>
						<input type="text" class="form-control text-right" name="field9">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 10</label>
						<input type="text" class="form-control text-right" name="field10">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 11</label>
						<input type="text" class="form-control text-right" name="field11">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 12</label>
						<input type="text" class="form-control text-right" name="field12">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 13</label>
						<input type="text" class="form-control text-right" name="field13">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 14</label>
						<input type="text" class="form-control text-right" name="field14">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 15</label>
						<input type="text" class="form-control text-right" name="field15">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 16</label>
						<input type="text" class="form-control text-right" name="field16">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 17</label>
						<input type="text" class="form-control text-right" name="field17">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 18</label>
						<input type="text" class="form-control text-right" name="field18">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 19</label>
						<input type="text" class="form-control text-right" name="field19">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 20</label>
						<input type="text" class="form-control text-right" name="field20">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 21</label>
						<input type="text" class="form-control text-right" name="field21">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 22</label>
						<input type="text" class="form-control text-right" name="field22">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 23</label>
						<input type="text" class="form-control text-right" name="field23">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 24</label>
						<input type="text" class="form-control text-right" name="field24">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 25</label>
						<input type="text" class="form-control text-right" name="field25">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 26</label>
						<input type="text" class="form-control text-right" name="field26">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 27</label>
						<input type="text" class="form-control text-right" name="field27">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 28</label>
						<input type="text" class="form-control text-right" name="field28">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 29</label>
						<input type="text" class="form-control text-right" name="field29">
					</div>
					<div class="col-md-4">
						<label class="control-label">Field 30</label>
						<input type="text" class="form-control text-right" name="field30">
					</div>
				</div>

			</form>
			<?php endif; ?>
			<!-- FORM Panel -->
      </div>
      <div class="modal-footer">
	  	<button type="button" class="btn btn-primary" id='submitScooter' onclick="$('#manage-scooter').submit()">Guardar</button>
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modal_scooter" >Cancelar</button>
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
	
	td{
		vertical-align: middle !important;
	}
	td p {
		margin: unset;
		padding: unset;
		line-height: 1em;
	}
</style>
<script>

	$('.view_incidents').click(function(){
		uni_modal("Lista de incidencias","view_incidents.php?scooterId="+$(this).attr('data-scooterId')+"&type="+$(this).attr('data-type'),"large")
	})

	$('.view_scooter_audit').click(function(){
		uni_modal("Lista de cambios","view_scooter_audit.php?scooterId="+$(this).attr('data-scooterId'),"large",false)
	})

	$('.new_incident').click(function(){
		uni_modal("Nueva incidencia","manage_incident.php?scooterId="+$(this).attr('data-id')+"&scooterNumber="+$(this).attr('data-scooterNumber'),"mid-small")
	})

	$('#filter-scooter').submit(function(e){
		e.preventDefault()
		location.href = 'index.php?page=scooters&'+$(this).serialize()
	})

	$('#categoryId').change(function(){
		if($(this).val() <= 0)
			return false;
		var scooter = $('#manage-scooter')
		scooter.find("[name='price']").val($(this).find('option:selected').attr('data-price'))
   	})

	$('#manage-scooter').on('reset',function(e){
		$('#msg').html('')
	})
	$('#manage-scooter').submit(function(e){
		e.preventDefault()
		if(!$('#manage-scooter')[0].checkValidity()){
			$('#manage-scooter')[0].reportValidity()
			return false;
		}
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'ajax.php?action=save_scooter',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Se agrego el patinete con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
				else if(resp==2){
					$('#msg').html('<div class="alert alert-danger">El numero de patinete ya existe</div>')
					end_load()
				}
			}
		})
	})

	$('.delete_scooter').click(function(){
		_conf("Esta seguro que desea eliminar el patinete?","delete_scooter",[$(this).attr('data-id')])
	})

	function delete_scooter($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_scooter',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Se elimino el patinete con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}else{
					alert_toast("Ocurrio un error al eliminar el patinete, existen clientes asignados al mismo",'danger')
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
				"targets": [2]
			}]
		}) 	

	});

	$('#new_scooter').click(function(){
			start_load()
			$('#modal_scooter').modal({
					show:true,
					backdrop:'static',
					keyboard:false,
					focus:true
			})
			end_load()
		})

		$('.edit_scooter').click(function(){
			console.log('asd');
			start_load();
			var scooter = $('#manage-scooter')
			scooter.get(0).reset()
			scooter.find("[name='id']").val($(this).attr('data-id'))
			scooter.find("[name='scooterNumber']").val($(this).attr('data-scooterNumber'))
			scooter.find("[name='description']").val($(this).attr('data-description'))
			scooter.find("[name='price']").val($(this).attr('data-price'))
			scooter.find("[name='categoryId']").val($(this).attr('data-categoryId'))
			scooter.find("[name='stateId']").val($(this).attr('data-stateId'))
			scooter.find("[name='partnerId']").val($(this).attr('data-partnerId'))
			scooter.find("[name='field1']").val($(this).attr('data-field1'))
			scooter.find("[name='field2']").val($(this).attr('data-field2'))
			scooter.find("[name='field3']").val($(this).attr('data-field3'))
			scooter.find("[name='field4']").val($(this).attr('data-field4'))
			scooter.find("[name='field5']").val($(this).attr('data-field5'))
			scooter.find("[name='field6']").val($(this).attr('data-field6'))
			scooter.find("[name='field7']").val($(this).attr('data-field7'))
			scooter.find("[name='field8']").val($(this).attr('data-field8'))
			scooter.find("[name='field9']").val($(this).attr('data-field9'))
			scooter.find("[name='field10']").val($(this).attr('data-field10'))
			scooter.find("[name='field11']").val($(this).attr('data-field11'))
			scooter.find("[name='field12']").val($(this).attr('data-field12'))
			scooter.find("[name='field13']").val($(this).attr('data-field13'))
			scooter.find("[name='field14']").val($(this).attr('data-field14'))
			scooter.find("[name='field15']").val($(this).attr('data-field15'))
			scooter.find("[name='field16']").val($(this).attr('data-field16'))
			scooter.find("[name='field17']").val($(this).attr('data-field17'))
			scooter.find("[name='field18']").val($(this).attr('data-field18'))
			scooter.find("[name='field19']").val($(this).attr('data-field19'))
			scooter.find("[name='field20']").val($(this).attr('data-field20'))
			scooter.find("[name='field21']").val($(this).attr('data-field21'))
			scooter.find("[name='field22']").val($(this).attr('data-field22'))
			scooter.find("[name='field23']").val($(this).attr('data-field23'))
			scooter.find("[name='field24']").val($(this).attr('data-field24'))
			scooter.find("[name='field25']").val($(this).attr('data-field25'))
			scooter.find("[name='field26']").val($(this).attr('data-field26'))
			scooter.find("[name='field27']").val($(this).attr('data-field27'))
			scooter.find("[name='field28']").val($(this).attr('data-field28'))
			scooter.find("[name='field29']").val($(this).attr('data-field29'))
			scooter.find("[name='field30']").val($(this).attr('data-field30'))

			$('#modal_scooter').modal({
					show:true,
					backdrop:'static',
					keyboard:false,
					focus:true
			})
			end_load()
		})
</script>
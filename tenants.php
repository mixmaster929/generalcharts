<?php 
	include('db_connect.php');
	date_default_timezone_set('Europe/Madrid');
	$status = isset($_GET['status']) && $_GET['status'] != -1 ? $_GET['status'] : null;
?>

<div class="container-fluid">
	
	<div class="col-lg-12">
		<div class="row mb-4 mt-4">
			<div class="col-md-12">
				
			</div>
		</div>
		<div class="row">
			<!-- FORM Panel -->

			<!-- Table Panel -->
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">
						<b>Lista de alquileres</b>
						<span class="float:right"><a class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="javascript:void(0)" data-userId="<?php echo($_SESSION['login_id']) ?>" data-type="<?php echo($_SESSION['login_type']) ?>"  id="new_tenant">
					<i class="fa fa-plus"></i> Nuevo alquiler
				</a></span>
					</div>
					<div class="card-body">
						<form id="filter-tenants">
							<div class="row form-group">
								<label class="control-label col-md-4 offset-md-1 text-right">Estado: </label>	
								<select name="status" id="status" class="custom-select col-md-2">
									<option <?php echo($status === null ? 'selected' : '') ?> value="-1">Todos</option>
									<option <?php echo($status == '1' ? 'selected' : '') ?> value="1">Activo</option>
									<option <?php echo($status == '2' ? 'selected' : '') ?> value="2">Recibido</option>
									<option <?php echo($status == '3' ? 'selected' : '') ?> value="3">Finalizado</option>
									<option <?php echo($status == '0' ? 'selected' : '') ?> value="0">Inactivo</option>
								</select>
								<button class="btn btn-sm btn-block btn-primary col-md-1 ml-1">Filtrar</button>
							</div>
						</form>
						<table class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Nombre</th>
									<th class="">Patinete rentado</th>
									<th class="">Tasa mensual</th>
									<th class="">Saldo pendiente</th>
									<th class="">Ultimo pago</th>
									<th class="">Estado</th>
									<th class="">Partner</th>
									<th class="text-center">Accion</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$query = "SELECT t.*,concat(t.lastName,', ',t.firstName,' ',t.middleName) as name,s.scooterNumber, s.partnerId as partnerScooter, st.name as scooterState, t.status, u.name as userName FROM tenants t ";
								$query .= " inner join scooters s on s.id = t.scooterId  ";
								$query .= " inner join states st on st.id = s.stateId  ";
								$query .= " left outer join users u on u.id = t.partnerId  ";
								$query .= " WHERE 1 = 1 ";
								$query .= $status !== null ? " AND t.status = $status " : ' ';
								$query .= " order by s.scooterNumber desc";
								$tenant = $conn->query($query);
								while($row=$tenant->fetch_assoc()):

									$dateFinish = $row['dateFinish'] ? $row['dateFinish'] : date('Y-m-d');

									$startDate = new DateTime(date('Y-m-d', strtotime($row['dateIn'])));
									$endDate = new DateTime(date('Y-m-d', strtotime($dateFinish)));

									$interval = $endDate->diff($startDate);
									$months = $interval->format('%m');
									$years = $interval->format('%y')*12;

									if($years == 0) $months++;
									$months = $months + $years;
									$payable = $row['price'] * $months;


									$paid = $conn->query("SELECT SUM(amount) as paid FROM payments where tenantId =".$row['id']." AND invoice != 'swap' AND status = 1");
									$last_payment = $conn->query("SELECT * FROM payments where tenantId =".$row['id']." AND invoice != 'swap' order by unix_timestamp(dateCreated) desc limit 1");
									$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
									$last_payment = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_payment->fetch_array()['dateCreated'])) : 'N/A';
									$outstanding = $payable - $paid;
									switch ($row['status']) {
										case 0:
											$status = 'Inactivo';
											break;

										case 1:
											$status = 'Activo';
											break;
										case 2:
											$status = 'Recibido';
											break;
										case 3:
											$status = 'Finalizado';
											break;
									}
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td>
										<?php echo ucwords($row['name']) ?>
									</td>
									<td class="">
										 <p> <b><?php echo $row['scooterNumber'] ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo number_format($row['price'],2) ?></b></p>
									</td>
									<td class="text-right">
										 <p> <b><?php echo number_format($outstanding,2) ?></b></p>
									</td>
									<td class="">
										 <p><b><?php echo  $last_payment ?></b></p>
									</td>
									<td class="">
										 <p><b><?php echo  $status ?></b></p>
									</td>
									<td class="">
										 <p><b><?php echo  $row['userName'] ? $row['userName'] : (intval($_SESSION['login_type']) !== ID_TYPE_PARTNER ? 'Propio' : 'Admin') ?></b></p>
									</td>
									<td class="text-center">
									<?php if($row['partnerScooter'] == intval($_SESSION['login_id']) && $row['scooterState'] == STATE_RESERVED && $row['status'] == 1) : ?>
										<button class="btn btn-sm btn-outline-info deliver_scooter" type="button" data-scooterId="<?php echo $row['scooterId'] ?>" data-id="<?php echo $row['id'] ?>"?>Entregar</button>
									<?php endif; ?>
									<?php if($row['status'] == 1) : ?>
										<button class="btn btn-sm btn-outline-primary edit_tenant" type="button" data-id="<?php echo $row['id'] ?>" data-userId="<?php echo($_SESSION['login_id']) ?>" data-type="<?php echo($_SESSION['login_type']) ?>" >Editar</button>
									<?php endif; ?>

									<?php if($row['status'] == 1 && (intval($_SESSION['login_type']) !== ID_TYPE_PARTNER || $row['partnerId'] == intval($_SESSION['login_id'])) ) : ?>
										<button class="btn btn-sm btn-outline-danger change_tenant_status" type="button" data-id="<?php echo $row['id'] ?>"  data-scooterId="<?php echo $row['scooterId'] ?>" data-status="0">Eliminar</button>
									<?php endif; ?>

									<?php if(intval($_SESSION['login_type']) !== ID_TYPE_PARTNER && $row['status'] == 1 || $row['status'] == 2) : ?>
									<button class="btn btn-sm btn-outline-success change_tenant_status" type="button" data-id="<?php echo $row['id'] ?>"  data-scooterId="<?php echo $row['scooterId'] ?>" data-status="3">Finalizar</button>
									<?php endif; ?>

									<?php if(intval($_SESSION['login_type']) == ID_TYPE_PARTNER && $row['status'] == 1 && $row['scooterState'] !== STATE_RESERVED) : ?>
										<button class="btn btn-sm btn-outline-success change_tenant_status" type="button" data-id="<?php echo $row['id'] ?>"  data-scooterId="<?php echo $row['scooterId'] ?>" data-status="<?php echo isset($row['partnerId']) ? '3' : '2' ?>"><?php echo isset($row['partnerId']) ? 'Finalizar' : 'Recibido' ?></button>
									<?php endif; ?>

									<?php if(intval($_SESSION['login_type']) !== ID_TYPE_PARTNER || intval($_SESSION['login_id']) == $row['partnerId']) : ?>
										<button class="btn btn-sm btn-outline-primary view_payment" type="button" data-id="<?php echo $row['id'] ?>" >Ver</button>
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
<div class="modal fade" id="confirm_finish_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title">Confirmación</h5>
      </div>
      <div class="modal-body">
        <div id="delete_content">Esta reguro que desea finalizar el alquiler?</div>
		<form id='manage-finish'>
			<div class="form-group">
				<div style="margin-top: 10px;">
					<label for="" class="control-label">Fecha de finalizacion</label>
					<input type="date" class="form-control" name="dateFinish" value="<?php echo date("Y-m-d")?>" required>
				</div>
			</div>
			<input type='hidden' name='id' id='finish_id'/>
			<input type='hidden' name='scooterId' id='finish_scooterId'/>
			<input type='hidden' name='status' id='finish_status'/>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='confirm' onclick="$('#manage-finish').submit()">Continuar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
      </div>
    </div>
  </div>
</div>
<style>
	
	td{
		vertical-align: middle !important;
	}
	td p{
		margin: unset
	}
	img{
		max-width:100px;
		max-height: :150px;
	}
</style>
<script>
	$(document).ready(function(){
		$('table').dataTable()
	})

	$('#filter-tenants').submit(function(e){
		e.preventDefault()
		location.href = 'index.php?page=tenants&'+$(this).serialize()
	})
	
	$('#new_tenant').click(function(){
		uni_modal("Nuevo alquiler","manage_tenant.php?userId="+$(this).attr('data-userId')+"&type="+$(this).attr('data-type'),"mid-large")
	})

	$('.view_payment').click(function(){
		uni_modal("Pagos de alquileres","view_payment.php?id="+$(this).attr('data-id'),"large",false)
		
	})
	$('.edit_tenant').click(function(){
		uni_modal("Administrar los detalles del alquiler","manage_tenant.php?id="+$(this).attr('data-id')+"&type="+$(this).attr('data-type')+"&userId="+$(this).attr('data-userId'),"mid-large")
		
	})
	$('.change_tenant_status').click(function(){
		switch (parseInt($(this).attr('data-status'))) {
			case 0:
				_conf("Esta reguro que desea eliminar el alquiler?","change_tenant_status",[$(this).attr('data-id'),$(this).attr('data-scooterId'),$(this).attr('data-status')])
				break;
			case 3:
				var finish = $('#manage-finish')
				finish.get(0).reset()
				finish.find("[name='id']").val($(this).attr('data-id'))
				finish.find("[name='scooterId']").val($(this).attr('data-scooterId'))
				finish.find("[name='status']").val($(this).attr('data-status'))
				$('#confirm_finish_modal').modal('show');
				//_conf("Esta reguro que desea finalizar el alquiler?","change_tenant_status",[$(this).attr('data-id'),$(this).attr('data-scooterId'),$(this).attr('data-status')])
				break;
			case 2:
				_conf("Esta reguro que desea cerrar el alquiler?","change_tenant_status",[$(this).attr('data-id'),$(this).attr('data-scooterId'),$(this).attr('data-status')])
				break;
		}
	})
	
	$('.deliver_scooter').click(function(){
		_conf("Esta reguro que desea entregar el scooter?","deliver_scooter",[$(this).attr('data-scooterId'),$(this).attr('data-id')])
	})

	$('#manage-finish').submit(function(e){
		e.preventDefault()
		if(!$('#manage-finish')[0].checkValidity()){
			$('#manage-finish')[0].reportValidity()
			return false;
		}
		start_load()
		$.ajax({
			url:'ajax.php?action=change_tenant_status',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Se finalizo el alquiler con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	})

	
	function deliver_scooter($scooterId,$id){
		start_load()
		$.ajax({
			url:'ajax.php?action=deliver_scooter',
			method:'POST',
			data:{scooterId:$scooterId,id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Se actualizo el alquiler con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}

	function change_tenant_status($id, $scooterId,$status){
		start_load()
		$.ajax({
			url:'ajax.php?action=change_tenant_status',
			method:'POST',
			data:{id:$id,scooterId:$scooterId,status:$status},
			success:function(resp){
				if(resp==1){
					alert_toast("Se actualizo el alquiler con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	}
</script>
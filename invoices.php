<?php include('db_connect.php');?>

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
						<b>Lista de pagos</b>
						<span class="float:right"><a data-userId="<?php echo($_SESSION['login_id']) ?>" data-type="<?php echo($_SESSION['login_type']) ?>" class="btn btn-primary btn-block btn-sm col-sm-2 float-right" href="javascript:void(0)" id="new_invoice">
					<i class="fa fa-plus"></i> Nuevo pago
				</a></span>
					</div>
					<div class="card-body">
						<table class="table table-condensed table-bordered table-hover">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="">Fecha</th>
									<th class="">Cliente</th>
									<th class="">Partner</th>
									<th class="">Factura</th>
									<th class="">Monto</th>
									<th class="">Estado</th>
									<th class="text-center">Accion</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$i = 1;
								$query = "SELECT p.*,concat(t.lastName,', ',t.firstName,' ',t.middleName) as name, t.id as tenantInd, u.name as nameUser FROM payments p inner join tenants t on t.id = p.tenantId LEFT OUTER JOIN users AS u ON u.id = p.partnerId";
								$query .= intval($_SESSION['login_type']) == ID_TYPE_PARTNER ? ' WHERE p.partnerId ='.$_SESSION['login_id'] : '';
								$query .= " order by date(p.dateCreated) desc";
								$invoices = $conn->query($query);
								
								while($row=$invoices->fetch_assoc()):
								?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td>
										<?php echo date('M d, Y',strtotime($row['dateCreated'])) ?>
									</td>
									<td class="">
										 <p> <b><?php echo ucwords($row['name']) ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo ucwords($row['nameUser']) ? ucwords($row['nameUser']) : 'Propio' ?></b></p>
									</td>
									<td class="">
										 <p> <b><?php echo ucwords($row['invoice']) ?></b></p>
									</td>
									<td class="text-right">
										 <p> <b><?php echo number_format($row['amount'],2) ?></b></p>
									</td>
									<td class="text-right">
										 <p> <b><?php echo ($row['status'] ? 'Confirmado' : 'En proceso') ?></b></p>
									</td>
									<td class="text-center">
										<button class="btn btn-sm btn-outline-primary view_payment" type="button" data-tenantId="<?php echo $row['tenantInd'] ?>" >Ver</button>
										<?php if($row['status'] == 0 && intval($_SESSION['login_type']) !== ID_TYPE_PARTNER) : ?>
											<button class="btn btn-sm btn-outline-success confirm_invoice" type="button" data-id="<?php echo $row['id'] ?>" >Confirmar</button>
										<?php endif; ?>
										<?php if($row['status'] == 0) : ?>
											<button class="btn btn-sm btn-outline-primary edit_invoice" type="button" data-id="<?php echo $row['id'] ?>" >Editar</button>
											<button class="btn btn-sm btn-outline-danger delete_invoice" type="button" data-id="<?php echo $row['id'] ?>">Eliminar</button>
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
	
	$('#new_invoice').click(function(){
		uni_modal("Nueva factura","manage_payment.php?userId="+$(this).attr('data-userId')+"&type="+$(this).attr('data-type'),"mid-large")
	})
	$('.edit_invoice').click(function(){
		uni_modal("Administrar el detalle de la factura","manage_payment.php?id="+$(this).attr('data-id'),"mid-large")
		
	})
	$('.delete_invoice').click(function(){
		_conf("Esta seguro que desea eliminar la factura?","delete_invoice",[$(this).attr('data-id')])
	})

	$('.confirm_invoice').click(function(){
		_conf("Esta seguro que desea confirmar el pago?","confirm_invoice",[$(this).attr('data-id')])
	})
	
	$('.view_payment').click(function(){
		uni_modal("Pagos de alquileres","view_payment.php?id="+$(this).attr('data-tenantId'),"large",false)
		
	})

	function confirm_invoice($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=confirm_invoice',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Se confirmo el pago con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}

	function delete_invoice($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_payment',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Se elimino la factura con exito",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}
</script>
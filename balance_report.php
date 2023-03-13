<?php 
include 'db_connect.php' ;
date_default_timezone_set('Europe/Madrid');
$user = null;
if(isset($_GET['userId']) && $_GET['userId'] !== '0' && $_GET['userId'] !== '-1'){
	$user = $_GET['userId'];
}else if(isset($_GET['userId']) && $_GET['userId'] == '-1'){
	$user = 'is null';
}
?>
<style>
	.on-print{
		display: none;
	}
</style>
<noscript>
	<style>
		.text-center{
			text-align:center;
		}
		.text-right{
			text-align:right;
		}
		table{
			width: 100%;
			border-collapse: collapse
		}
		tr,td,th{
			border:1px solid black;
		}
	</style>
</noscript>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<div class="col-md-12">
						<?php if(intval($_SESSION['login_type']) != ID_TYPE_PARTNER): ?>
							<form id="filter-report">
								<div class="row form-group">
									<label class="control-label col-md-2 text-right">Partner: </label>
									<select name="userId" id="userId" class="custom-select ml-3 col-md-2">
										<option value="0">Todos</option>
										<option value="-1">Propios</option>
										<?php 
											$users = $conn->query("SELECT id, name, username FROM users where type = 2");
											if($users->num_rows > 0):
												while($row= $users->fetch_assoc()) :
										?>
											<option <?php echo($user == $row['username'] ? 'selected' : '')  ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
										<?php endwhile; ?>
										<?php endif; ?>
									</select>
									<button class="btn btn-sm btn-block btn-primary col-md-1 ml-3">Filtrar</button>
								</div>
							</form>
						<?php endif; ?>
					<hr>
						<div class="row">
							<div class="col-md-12 mb-2">
							<button class="btn btn-sm btn-block btn-success col-md-2 ml-1 float-right" type="button" id="print"><i class="fa fa-print"></i> Print</button>
							</div>
						</div>
					<div id="report">
						<div class="on-print">
							 <p><center>Informe de saldos de alquiler</center></p>
							 <p><center>A partir de <b><?php echo date('F ,Y') ?></b></center></p>
						</div>
						<div class="row">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<th>Partner</th>
										<th>Cliente</th>
										<th>Scooter #</th>
										<th>Tarifa Mensual</th>
										<th>Meses a pagar</th>
										<th>Importe a pagar</th>
										<th>Pagado</th>
										<th>Saldo pendiente</th>
										<th>Último pago</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$i = 1;
									// $tamount = 0;
									$query = "SELECT t.*,concat(t.lastName,', ',t.firstName,' ',t.middleName) as name,s.scooterNumber, u.username FROM tenants t inner join scooters s on s.id = t.scooterId left outer join users as u on u.id = t.partnerId where (t.status = 1 OR t.status = 3) ";
									$query .= intval($_SESSION['login_type']) == ID_TYPE_PARTNER ? " AND t.partnerId =".$_SESSION['login_id'] : '';
									$query .= $user && $user !== 'is null' ? " AND t.partnerId = $user " : ($user === 'is null' ? " AND t.partnerId $user" : '');
									$query .= " order by s.scooterNumber desc ";
									$tenants =$conn->query($query);
									if($tenants->num_rows > 0):
									while($row=$tenants->fetch_assoc()):

										$dateFinish = $row['dateFinish'] ? $row['dateFinish'] : date('Y-m-d');

										$startDate = new DateTime(date('Y-m-d', strtotime($row['dateIn'])));
										$endDate = new DateTime(date('Y-m-d', strtotime($dateFinish)));
										
										$interval = $endDate->diff($startDate);
										$months = $interval->format('%m');
										$years = $interval->format('%y')*12;

										if($years == 0) $months++;
										$months = $months + $years;
										$payable = $row['price'] * $months;

										$paid = $conn->query("SELECT SUM(amount) as paid FROM payments where tenantId =".$row['id']." AND status = 1 AND invoice != 'swap'");
										$last_payment = $conn->query("SELECT * FROM payments where tenantId =".$row['id']." AND status = 1 AND invoice != 'swap' order by unix_timestamp(dateCreated) desc limit 1");
										$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
										$last_payment = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_payment->fetch_array()['dateCreated'])) : 'N/A';
										$outstanding = $payable - $paid;
									?>
									<tr>
										<td><?php echo $i++ ?></td>
										<td><?php echo $row['username'] ? $row['username'] : 'Propio' ?></td>
										<td><?php echo ucwords($row['name']) ?></td>
										<td><?php echo $row['scooterNumber'] ?></td>
										<td class="text-right"><?php echo number_format($row['price'],2) ?></td>
										<td class="text-right"><?php echo $months.' mo/s' ?></td>
										<td class="text-right"><?php echo number_format($payable,2) ?></td>
										<td class="text-right"><?php echo number_format($paid,2) ?></td>
										<td class="text-right"><?php echo number_format($outstanding,2) ?></td>
										<td><?php echo date('M d,Y',strtotime($last_payment)) ?></td>
									</tr>
								<?php endwhile; ?>
								<?php else: ?>
									<tr>
										<th colspan="9"><center>Sin registros.</center></th>
									</tr>
								<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('#print').click(function(){
		var _style = $('noscript').clone()
		var _content = $('#report').clone()
		var nw = window.open("","_blank","width=800,height=700");
		nw.document.write(_style.html())
		nw.document.write(_content.html())
		nw.document.close()
		nw.print()
		setTimeout(function(){
		nw.close()
		},500)
	})
	$('#filter-report').submit(function(e){
		e.preventDefault()
		location.href = 'index.php?page=balance_report&'+$(this).serialize()
	})
</script>
<?php include 'db_connect.php' ?>
<?php 

$month_of = isset($_GET['month_of']) ? $_GET['month_of'] : date('Y-m');
$totalFinalAmount = 0; $totalAmount=0; $totalPartnertAmount=0;

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
					<form id="filter-report">
						<div class="row form-group">
							<label class="control-label mr-4">Mes desde: </label>
							<input type="month" name="month_of" class='from-control col-md-4' value="<?php echo ($month_of) ?>">
							<label class="control-label ml-4">Partner: </label>
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
							<button class="btn btn-sm btn-block btn-primary col-md-2 ml-4">Filtrar</button>
						</div>
					</form>
					<hr>
						<div class="row">
							<div class="col-md-12 mb-2">
							<button class="btn btn-sm btn-block btn-success col-md-2 ml-1 float-right" type="button" id="print"><i class="fa fa-print"></i> Imprimir</button>
							</div>
						</div>
					<div id="report">
						<div class="on-print">
							 <p><center>Informe de pagos de alquiler</center></p>
							 <p><center>Por el mes de <b><?php echo date('F ,Y',strtotime($month_of.'-1')) ?></b></center></p>
						</div>
						<div class="row">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>#</th>
										<th>Fecha</th>
										<th>Cliente</th>
										<th>Partner</th>
										<th>Patinete #</th>
										<th>Factura</th>
										<th>Subtotal</th>
										<th>Total Partner</th>
										<th>Total</th>
									</tr>
								</thead>
								<tbody>
									<?php 
									$i = 1;
									$finalAmount = 0;
									$partnertAmount = 0;
									$query = "SELECT p.*,concat(t.lastName,', ',t.firstName,' ',t.middleName) as name,s.scooterNumber, u.name as username FROM payments p";
									$query .= " inner join tenants t on t.id = p.tenantId";
									$query .= " inner join scooters s on s.id = t.scooterId";
									$query .= " left outer join users as u on u.id = p.partnerId";
									$query .= " where date_format(p.dateCreated,'%Y-%m') = '$month_of'";
									$query .= $user && $user !== 'is null' ? " AND t.partnerId = $user " : ($user === 'is null' ? " AND t.partnerId $user" : '');
									$query .= " and p.status = 1 order";
									$query .= " by unix_timestamp(p.dateCreated)  asc";
									$payments  = $conn->query($query);
									if($payments->num_rows > 0 ):
									while($row=$payments->fetch_assoc()):
										if($row['invoice'] != 'swap'){
											$totalAmount += $row['amount'];
										}
										$totalFinalAmount += $row['final_amount'];
										$totalPartnertAmount += $row['partner_amount'];
									?>
									<tr>
										<td><?php echo $i++ ?></td>
										<td><?php echo date('M d,Y',strtotime($row['dateCreated'])) ?></td>
										<td><?php echo ucwords($row['name']) ?></td>
										<td><?php echo $row['username'] ? $row['username'] : 'Propio' ?></td>
										<td><?php echo $row['scooterNumber'] ?></td>
										<td><?php echo $row['invoice'] ?></td>
										<td class="text-right"><?php echo $row['invoice'] !== 'swap' ? number_format($row['amount'],2) : 0 ?></td>
										<td class="text-right"><?php echo number_format($row['partner_amount'],2) ?></td>
										<td class="text-right"><?php echo number_format($row['final_amount'],2) ?></td>

									</tr>
								<?php endwhile; ?>
								<?php else: ?>
									<tr>
										<th colspan="6"><center>Sin registros.</center></th>
									</tr>
								<?php endif; ?>
								</tbody>
								<tfoot>
									<tr>
										<th colspan="6">Cantidad total</th>
										<th class='text-right'><?php echo number_format($totalAmount,2) ?></th>
										<th class='text-right'><?php echo number_format($totalPartnertAmount,2) ?></th>
										<th class='text-right'><?php echo number_format($totalFinalAmount,2) ?></th>
									</tr>
								</tfoot>
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
		location.href = 'index.php?page=payment_report&'+$(this).serialize()
	})
</script>
<?php include 'db_connect.php';
date_default_timezone_set('Europe/Madrid');?>

<?php 
$tenants =$conn->query("SELECT t.*,concat(t.lastName,', ',t.firstName,' ',t.middleName) as name,s.scooterNumber FROM tenants t inner join scooters s on s.id = t.scooterId where t.id = {$_GET['id']} ");
foreach($tenants->fetch_array() as $k => $v){
	if(!is_numeric($k)){
		$$k = $v;
	}
}


$calculateFinishDate = $dateFinish ? $dateFinish : date('Y-m-d');

$startDate = new DateTime(date('Y-m-d', strtotime($dateIn)));
$endDate = new DateTime(date('Y-m-d', strtotime($calculateFinishDate)));

$interval = $endDate->diff($startDate);
$months = $interval->format('%m');
$years = $interval->format('%y')*12;

if($years == 0) $months++;
$months = $months + $years;
$payable = $price * $months;


$paid = $conn->query("SELECT SUM(amount) as paid FROM payments where tenantId =".$_GET['id']." AND invoice != 'swap' AND status = 1");
$last_payment = $conn->query("SELECT * FROM payments where tenantId =".$_GET['id']." AND invoice != 'swap' AND status = 1 order by unix_timestamp(dateCreated) desc limit 1");
$paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
$last_payment = $last_payment->num_rows > 0 ? date("M d, Y",strtotime($last_payment->fetch_array()['dateCreated'])) : 'N/A';
$outstanding = $payable - $paid;

?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-4">
				<div id="details">
					<large><b>Detalles</b></large>
					<hr>
					<p>Cliente: <b><?php echo ucwords($name) ?></b></p>
					<p>Tarifa de alquiler mensual: <b><?php echo number_format($price,2) ?></b></p>
					<p>Saldo pendiente: <b><?php echo number_format($outstanding,2) ?></b></p>
					<p>Total pagado: <b><?php echo number_format($paid,2) ?></b></p>
					<p>Alquiler iniciado: <b><?php echo date("M d, Y",strtotime($dateIn)) ?></b></p>
					<p>Alquiler finalizado: <b><?php echo $dateFinish ? date("M d, Y",strtotime($dateFinish)) : '-' ?></b></p>
					<p>Meses a pagar: <b><?php echo $months ?></b></p>
				</div>
			</div>
			<div class="col-md-8">
				<large><b>Lista de pagos</b></large>
					<hr>
				<table class="table table-condensed table-striped">
					<thead>
						<tr>
							<th>Fecha</th>
							<th>Factura</th>
							<th>Monto</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$payments = $conn->query("SELECT * FROM payments where tenantId = $id AND invoice != 'swap' AND status = 1");
						if($payments->num_rows > 0):
						while($row=$payments->fetch_assoc()):
						?>
					<tr>
						<td><?php echo date("M d, Y",strtotime($row['dateCreated'])) ?></td>
						<td><?php echo $row['invoice'] ?></td>
						<td class='text-right'><?php echo number_format($row['amount'],2) ?></td>
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
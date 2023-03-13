<?php include 'db_connect.php' ?>
<?php 

$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d');
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d');
$user = isset($_GET['userId']) && $_GET['userId'] !== '0' ? $_GET['userId'] : null;

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
							<label class="control-label text-right">Desde: </label>
							<input type="date" name="dateFrom" class='from-control col-md-2  ml-3' value="<?php echo ($dateFrom) ?>">
							<label class="control-label text-right  ml-3">Hasta: </label>
							<input type="date" name="dateTo" class='from-control col-md-2  ml-3' value="<?php echo ($dateTo) ?>">

                            <label class="control-label col-md-2 text-right">Usuario: </label>
                            <select name="userId" id="userId" class="custom-select ml-3 col-md-2">
                                <option value="0">Todos</option>
                                <?php 
                                    $users = $conn->query("SELECT name, username FROM users");
                                    if($users->num_rows > 0):
                                        while($row= $users->fetch_assoc()) :
                                ?>
                                    <option <?php echo($user == $row['username'] ? 'selected' : '')  ?> value="<?php echo $row['username'] ?>"><?php echo $row['name'] ?></option>
                                <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                            <button class="btn btn-sm btn-block btn-primary col-md-1 ml-3">Filtrar</button>

                        </div>
					</form>
					<hr>
					<div id="report">
						<div >
                        <table class="table table-condensed table-bordered table-hover">
								<thead>
									<tr>
										<th>Fecha</th>
                                        <th>Usuario</th>
										<th>Componente</th>
										<th>Accion</th>
                                        <th>Historial</th>
									</tr>
								</thead>
								<tbody>
									<?php 
                                    $query = "SELECT id, component, action, oldObject, newObject, date, username, null as scooterId FROM audits WHERE 1 = 1";
                                    $query .= isset($user) ? " AND username = '" . $user . "'" : '';
                                    $query .= isset($dateFrom) &&  isset($dateTo)? " AND date >= '" . $dateFrom . " 00:00:00 ' AND date <= '" . $dateTo . " 23:59:59'" : '';
                                    $query .= " UNION SELECT id, null as component, action, oldObject, newObject, date, username, scooterId FROM scooter_audits WHERE 1 = 1";
                                    $query .= isset($user) && $user !== '0' ? " AND username = '" . $user . "'" : '';
                                    $query .= isset($dateFrom) &&  isset($dateTo) ? " AND date >= '" . $dateFrom . " 00:00:00 ' AND date <= '" . $dateTo . " 23:59:59'" : '';
									$query .= isset($user) && $user !== '0' ? " AND username = '" . $user . "'" : '';
									$audits = $conn->query($query);
									if($audits->num_rows > 0 ):
									while($row=$audits->fetch_assoc()):
									?>
									<tr>
										<td><?php echo $row['date'] ?></td>
										<td><?php echo $row['username'] ?></td>
										<td><?php echo (isset($row['component']) ? $row['component'] : 'SCOOTER') ?></td>
										<td><?php echo $row['action'] ?></td>
                                        <td><button class="btn btn-sm btn-info view_audit" type="button" data-scooterId="<?php echo (isset($row['scooterId']) ? $row['scooterId'] : 0) ?>" data-id="<?php echo $row['id'] ?>">Ver</button></td>
									</tr>
								<?php endwhile; ?>
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
    $(document).ready(function(){
		$('table').dataTable()

        $('#filter-report').submit(function(e){
            e.preventDefault()
            location.href = 'index.php?page=global_audit_report&'+$(this).serialize()
	    })
	})
    $('.view_audit').click(function(){
            uni_modal("Lista de cambios","view_audit.php?id="+$(this).attr('data-id')+"&scooterId="+$(this).attr('data-scooterId'),"mid-large",false)
        })
</script>
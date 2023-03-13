<?php include 'db_connect.php' ?>
<?php 

$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d');
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d');
$user = isset($_GET['userId']) && $_GET['userId'] !== 0 ? $_GET['userId'] : null;

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
							<label class="control-label text-right ">Fecha desde: </label>
							<input type="date" name="dateFrom" class='from-control col-md-2 ' value="<?php echo ($dateFrom) ?>">
							<label class="control-label text-right ml-3">Fecha Hasta: </label>
							<input type="date" name="dateTo" class='from-control col-md-2' value="<?php echo ($dateTo) ?>">
                            <label class="control-label col-md-1 text-right">User: </label>
                            <select name="userId" id="userId" class="custom-select col-md-2">
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
                            <button class="btn btn-sm btn-block btn-primary col-md-2">Filtrar</button>

                        </div>
					</form>
					<hr>
					<div id="report">
						<div >
                        <table class="table-incident table-condensed table-bordered table-hover">
								<thead>
									<tr>
										<th>#</th>
										<th>Descripcion</th>
                                        <th>Fecha de creacion</th>
										<th>Fecha de actualizacion</th>
										<th>Creada por</th>
                                        <th>Actualizada por</th>
                                        <th>Estado</th>
									</tr>
								</thead>
								<tbody>
									<?php 
                                    $query = "SELECT * FROM scooter_incidents WHERE 1 = 1";
                                    $query .= isset($user) && $user != '0' ? " AND (createdBy = '" . $user . "' OR updatedBy ='" . $user . "')" : '';
                                    $query .= isset($dateFrom) &&  isset($dateTo) ? " AND (createdAd >= '" . $dateFrom . " 00:00:00 ' AND createdAd <= '" . $dateTo . " 23:59:59'" : '';
                                    $query .= isset($dateFrom) &&  isset($dateTo) ? " OR updatedAt >= '" . $dateFrom . " 00:00:00 ' AND updatedAt <= '" . $dateTo . " 23:59:59')" : '';
									$audits = $conn->query($query);
									if($audits->num_rows > 0 ):
									while($row=$audits->fetch_assoc()):
									?>
									<tr>
										<td><?php echo $row['scooterNumber'] ?></td>
                                        <td><?php echo $row['description'] ?></td>
										<td><?php echo date('Y-m-d',strtotime($row['createdAd'])) ?></td>
                                        <td><?php echo date('Y-m-d',strtotime($row['updatedAt'])) ?></td>
										<td><?php echo $row['createdBy'] ?></td>
                                        <td><?php echo $row['updatedBy'] ?></td>
										<td><?php echo($row['resolved'] ? 'Resuelta' : 'Sin resolver')?></td>
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
		$('.table-incident').dataTable()

        $('#filter-report').submit(function(e){
            e.preventDefault()
            location.href = 'index.php?page=incident_report&'+$(this).serialize()
	    })
	})
</script>
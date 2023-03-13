<?php 
include 'db_connect.php';
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-4">
							<div class="card border-primary">
								<div class="card-body bg-light">
									<h4><b>Reporte global de auditoria</b></h4>
								</div>
								<div class="card-footer">
									<div class="col-md-12">
										<a href="index.php?page=global_audit_report" class="d-flex justify-content-between"> <span>Ver informe</span> <span class="fa fa-chevron-circle-right"></span></a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="card border-primary">
								<div class="card-body bg-light">
									<h4><b>Reporte de incidencias</b></h4>
								</div>
								<div class="card-footer">
									<div class="col-md-12">
										<a href="index.php?page=incident_report" class="d-flex justify-content-between"> <span>Ver informe</span> <span class="fa fa-chevron-circle-right"></span></a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
include 'db_connect.php';
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<div class="col-md-12">
					<div class="row">
						<?php if(intval($_SESSION['login_type']) != ID_TYPE_PARTNER): ?>
							<div class="col-sm-4">
								<div class="card border-primary">
									<div class="card-body bg-light">
										<h4><b>Informe de pagos mensuales</b></h4>
									</div>
									<div class="card-footer">
										<div class="col-md-12">
											<a href="index.php?page=payment_report" class="d-flex justify-content-between"> <span>Ver informe</span> <span class="fa fa-chevron-circle-right"></span></a>
										</div>
									</div>
								</div>
							</div>
						<?php endif; ?>
						<div class="col-sm-4">
							<div class="card border-primary">
								<div class="card-body bg-light">
									<h4><b>Informe de saldos de alquiler</b></h4>
								</div>
								<div class="card-footer">
									<div class="col-md-12">
										<a href="index.php?page=balance_report" class="d-flex justify-content-between"> <span>Ver informe</span> <span class="fa fa-chevron-circle-right"></span></a>
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
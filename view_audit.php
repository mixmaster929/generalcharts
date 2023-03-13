<?php include 'db_connect.php';
include 'constants.php'; ?>

<?php 
$id = isset($_GET['id']) ? $_GET['id'] : '';
$scooterId = isset($_GET['scooterId']) ? $_GET['scooterId'] : '';

if($scooterId){
	$history = $conn->query("SELECT oldObject, newObject FROM scooter_audits WHERE id = $id");
}else{
	$history = $conn->query("SELECT oldObject, newObject FROM audits WHERE id = $id");
}
$history = mysqli_fetch_object($history);
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-12" style="word-wrap: break-word;">
				<p><b>Antes: <?php echo $history->oldObject ?></b></p>
                <p><b>Despues: <?php echo $history->newObject ?></b></p>
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
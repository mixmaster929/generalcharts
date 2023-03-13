<?php
include 'constants.php'; 
?>
<style>
	.collapse a{
		text-indent:10px;
	}
	nav#sidebar{
		/*background: url(assets/uploads/<?php echo $_SESSION['system']['cover_img'] ?>) !important*/
	}
</style>

<nav id="sidebar" class='mx-lt-5 bg-dark' >
		
		<div class="sidebar-list">
				<?php if(intval($_SESSION['login_type']) == ID_TYPE_PARTNER): ?>
				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-tachometer-alt "></i></span> Dashboard</a>
				<a href="index.php?page=scooters" class="nav-item nav-houses"><span class='icon-field'><i class="fa fa-home "></i></span> Patinetes</a>
				<a href="index.php?page=tenants" class="nav-item nav-tenants"><span class='icon-field'><i class="fa fa-user-friends "></i></span> Alquileres</a>
				<a href="index.php?page=invoices" class="nav-item nav-invoices"><span class='icon-field'><i class="fa fa-file-invoice "></i></span> Pagos</a>
				<a href="index.php?page=reports" class="nav-item nav-reports"><span class='icon-field'><i class="fa fa-list-alt "></i></span> Reportes</a>
				<?php else: ?>
				<a href="index.php?page=home" class="nav-item nav-home"><span class='icon-field'><i class="fa fa-tachometer-alt "></i></span> Dashboard</a>
				<a href="index.php?page=categories" class="nav-item nav-categories"><span class='icon-field'><i class="fa fa-th-list "></i></span> Tipo de patinete</a>
				<a href="index.php?page=states" class="nav-item nav-states"><span class='icon-field'><i class="fa fa-th-list"></i></span> Tipo de estados</a>
				<a href="index.php?page=scooters" class="nav-item nav-houses"><span class='icon-field'><i class="fa fa-home "></i></span> Patinetes</a>
				<a href="index.php?page=tenants" class="nav-item nav-tenants"><span class='icon-field'><i class="fa fa-user-friends "></i></span> Alquileres</a>
				<a href="index.php?page=invoices" class="nav-item nav-invoices"><span class='icon-field'><i class="fa fa-file-invoice "></i></span> Pagos</a>
				<a href="index.php?page=reports" class="nav-item nav-reports"><span class='icon-field'><i class="fa fa-list-alt "></i></span> Reportes</a>
				<a href="index.php?page=audit" class="nav-item nav-reports"><span class='icon-field'><i class="fa fa-history"></i></span> Auditoria</a>
				<a href="index.php?page=users" class="nav-item nav-users"><span class='icon-field'><i class="fa fa-users "></i></span> Usuarios</a>
				<?php endif; ?>
				<!-- <a href="index.php?page=site_settings" class="nav-item nav-site_settings"><span class='icon-field'><i class="fa fa-cogs text-danger"></i></span> System Settings</a> -->
		</div>

</nav>
<script>
	$('.nav_collapse').click(function(){
		$($(this).attr('href')).collapse()
	})
	$('.nav-<?php echo isset($_GET['page']) ? $_GET['page'] : '' ?>').addClass('active')
</script>

<?php
ob_start();
$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();
if($action == 'login'){
	$login = $crud->login();
	if($login)
		echo $login;
}
if($action == 'login2'){
	$login = $crud->login2();
	if($login)
		echo $login;
}
if($action == 'logout'){
	$logout = $crud->logout();
	if($logout)
		echo $logout;
}
if($action == 'logout2'){
	$logout = $crud->logout2();
	if($logout)
		echo $logout;
}
if($action == 'save_user'){
	$save = $crud->save_user();
	if($save)
		echo $save;
}
if($action == 'delete_user'){
	$save = $crud->delete_user();
	if($save)
		echo $save;
}
if($action == 'signup'){
	$save = $crud->signup();
	if($save)
		echo $save;
}
if($action == 'update_account'){
	$save = $crud->update_account();
	if($save)
		echo $save;
}
if($action == "save_settings"){
	$save = $crud->save_settings();
	if($save)
		echo $save;
}
if($action == "save_category"){
	$save = $crud->save_category();
	if($save)
		echo $save;
}
if($action == "delete_category"){
	$delete = $crud->delete_category();
	if($delete)
		echo $delete;
}
if($action == "save_scooter"){
	$save = $crud->save_scooter();
	if($save)
		echo $save;
}
if($action == "delete_scooter"){
	$save = $crud->delete_scooter();
	if($save)
		echo $save;
}

if($action == "save_tenant"){
	$save = $crud->save_tenant();
	if($save)
		echo $save;
}
if($action == "change_tenant_status"){
	$save = $crud->change_tenant_status();
	if($save)
		echo $save;
}
if($action == "get_tdetails"){
	$get = $crud->get_tdetails();
	if($get)
		echo $get;
}

if($action == "save_payment"){
	$save = $crud->save_payment();
	if($save)
		echo $save;
}
if($action == "delete_payment"){
	$delete = $crud->delete_payment();
	if($delete)
		echo $delete;
}
if($action == "save_state"){
	$save = $crud->save_state();
	if($save)
		echo $save;
}
if($action == "delete_state"){
	$delete = $crud->delete_state();
	if($delete)
		echo $delete;
}
if($action == "save_incident"){
	$save = $crud->save_incident();
	if($save)
		echo $save;
}
if($action == "resolve_incident"){
	$save = $crud->resolve_incident();
	if($save)
		echo $save;
}
if($action == "deliver_scooter"){
	$save = $crud->deliver_scooter();
	if($save)
		echo $save;
}
if($action == "confirm_invoice"){
	$save = $crud->confirm_invoice();
	if($save)
		echo $save;
}

ob_end_flush();
?>

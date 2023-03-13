<?php
session_start();
ini_set('display_errors', 1);
include 'constants.php'; 

class Action
{
    private $db;

    public function __construct()
    {
        date_default_timezone_set('Europe/Madrid');
        ob_start();
        include 'db_connect.php';
        $this->db = $conn;
    }
    public function __destruct()
    {
        $this->db->close();
        ob_end_flush();
    }

    public function login()
    {

        extract($_POST);
        $qry = $this->db->query("SELECT * FROM users where username = '" . $username . "' and password = '" . md5($password) . "' ");
        if ($qry->num_rows > 0) {
            foreach ($qry->fetch_array() as $key => $value) {
                if ($key != 'passwors' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }
            }
            return 1;
        } else {
            return 3;
        }
    }
    public function login2()
    {

        extract($_POST);
        if (isset($email)) {
            $username = $email;
        }

        $qry = $this->db->query("SELECT * FROM users where username = '" . $username . "' and password = '" . md5($password) . "' ");
        if ($qry->num_rows > 0) {
            foreach ($qry->fetch_array() as $key => $value) {
                if ($key != 'passwors' && !is_numeric($key)) {
                    $_SESSION['login_' . $key] = $value;
                }

            }
            if ($_SESSION['login_alumnus_id'] > 0) {
                $bio = $this->db->query("SELECT * FROM alumnus_bio where id = " . $_SESSION['login_alumnus_id']);
                if ($bio->num_rows > 0) {
                    foreach ($bio->fetch_array() as $key => $value) {
                        if ($key != 'passwors' && !is_numeric($key)) {
                            $_SESSION['bio'][$key] = $value;
                        }

                    }
                }
            }
            if ($_SESSION['bio']['status'] != 1) {
                foreach ($_SESSION as $key => $value) {
                    unset($_SESSION[$key]);
                }
                return 2;
                exit;
            }
            return 1;
        } else {
            return 3;
        }
    }
    public function logout()
    {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:login.php");
    }
    public function logout2()
    {
        session_destroy();
        foreach ($_SESSION as $key => $value) {
            unset($_SESSION[$key]);
        }
        header("location:../index.php");
    }

    public function save_user()
    {
        extract($_POST);
        $data = " name = '$name' ";
        $data .= ", username = '$username' ";
        if (!empty($password)) {
            $data .= ", password = '" . md5($password) . "' ";
        }
        $date = date('Y-m-d H:i:s');
        $data .= ", dateCreated = '$date' ";
        
        $this->db->query("START TRANSACTION");
        if (empty($id)) {
            $chk = $this->db->query("Select * from users where username = '$username'")->num_rows;
            if ($chk > 0) {
                return 2;
                exit;
            }
            $save = $this->db->query("INSERT INTO users set " . $data);
            $id = $this->db->insert_id;
        } else {
            $chk = $this->db->query("Select * from users where username = '$username' and id !='$id' ")->num_rows;
            if ($chk > 0) {
                return 2;
                exit;
            }
            $save = $this->db->query("UPDATE users set " . $data . " where id = " . $id);
        }
        if ($save && $id && isset($type) && $type == ID_TYPE_PARTNER) {
            //Guardo las reglas de pago si es partner
            $rules = json_decode($rules);
            foreach ($rules as $rule) {
                if($rule->id || $rule->enable){
                    $data = " partnerId = '$id' ";
                    $result = false;
                    $enable = 0;
                    $ruleId = 0;
                    $table = '';
                    switch (strtolower($rule->name)) {
                            case strtolower(MONTHLY_IVA):
                                $data .= " , amount = '$rule->amount' ";
                                $data .= " , months = '$rule->months' ";
                                $enable = $rule->enable;
                                $ruleId = $rule->id;
                                $table = 'partner_payment_monthly_rules';
                            break;
                        
                            case strtolower(ETENER_IVA):
                                $data .= " , percentage = '$rule->percentage' ";
                                $enable = $rule->enable;
                                $ruleId = $rule->id;
                                $table = 'partner_payment_iva_rules';
                            break;

                            case strtolower(SWAP_IVA):
                                $data .= " , amount = '$rule->amount' ";
                                $enable = $rule->enable;
                                $ruleId = $rule->id;
                                $table = 'partner_payment_swap_rules';
                            break;
                    }

                    //Es una regla existente
                    if($ruleId){
                        // Si esta activa la actualizo
                        if($enable){
                            $result = $this->db->query("UPDATE $table set " . $data . " where id = " . $ruleId);
                        // Si se desactivo la elimino
                        }else{
                            $result = $this->db->query("DELETE FROM $table where id = " . $ruleId);
                        }
                    }else{
                        // Si no existe la creo
                        $result = $this->db->query("INSERT INTO $table set " . $data);
                    }
                    if(!$result){
                        $this->db->query("ROLLBACK");
                        return 3;
                    }
                }
            }
            $this->db->query("COMMIT");
            return 1;
        }else if($save){
            $this->db->query("COMMIT");
            return 1;
        }
        $this->db->query("ROLLBACK");
        return 3;
    }
    public function delete_user()
    {
        extract($_POST);
        $delete = false;
        $delete = $this->db->query("DELETE FROM partner_payment_iva_rules where partnerId = " . $id);
        $delete = $this->db->query("DELETE FROM partner_payment_monthly_rules where partnerId = " . $id);
        $delete = $this->db->query("DELETE FROM users where id = " . $id);

        if ($delete) {
            return 1;
        }
        return 0;

    }
    public function signup()
    {
        extract($_POST);
        $data = " name = '" . $firstname . ' ' . $lastname . "' ";
        $data .= ", username = '$email' ";
        $data .= ", password = '" . md5($password) . "' ";
        $chk = $this->db->query("SELECT * FROM users where username = '$email' ")->num_rows;
        if ($chk > 0) {
            return 2;
            exit;
        }
        $save = $this->db->query("INSERT INTO users set " . $data);
        if ($save) {
            $uid = $this->db->insert_id;
            $data = '';
            foreach ($_POST as $k => $v) {
                if ($k == 'password') {
                    continue;
                }

                if (empty($data) && !is_numeric($k)) {
                    $data = " $k = '$v' ";
                } else {
                    $data .= ", $k = '$v' ";
                }

            }
            if ($_FILES['img']['tmp_name'] != '') {
                $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
                $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
                $data .= ", avatar = '$fname' ";

            }
            $save_alumni = $this->db->query("INSERT INTO alumnus_bio set $data ");
            if ($data) {
                $aid = $this->db->insert_id;
                $this->db->query("UPDATE users set alumnus_id = $aid where id = $uid ");
                $login = $this->login2();
                if ($login) {
                    return 1;
                }

            }
        }
    }
    public function update_account()
    {
        extract($_POST);
        $data = " name = '" . $firstname . ' ' . $lastname . "' ";
        $data .= ", username = '$email' ";
        if (!empty($password)) {
            $data .= ", password = '" . md5($password) . "' ";
        }

        $chk = $this->db->query("SELECT * FROM users where username = '$email' and id != '{$_SESSION['login_id']}' ")->num_rows;
        if ($chk > 0) {
            return 2;
            exit;
        }
        $save = $this->db->query("UPDATE users set $data where id = '{$_SESSION['login_id']}' ");
        if ($save) {
            $data = '';
            foreach ($_POST as $k => $v) {
                if ($k == 'password') {
                    continue;
                }

                if (empty($data) && !is_numeric($k)) {
                    $data = " $k = '$v' ";
                } else {
                    $data .= ", $k = '$v' ";
                }

            }
            if ($_FILES['img']['tmp_name'] != '') {
                $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
                $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
                $data .= ", avatar = '$fname' ";

            }
            $save_alumni = $this->db->query("UPDATE alumnus_bio set $data where id = '{$_SESSION['bio']['id']}' ");
            if ($data) {
                foreach ($_SESSION as $key => $value) {
                    unset($_SESSION[$key]);
                }
                $login = $this->login2();
                if ($login) {
                    return 1;
                }

            }
        }
    }

    public function save_settings()
    {
        extract($_POST);
        $data = " name = '" . str_replace("'", "&#x2019;", $name) . "' ";
        $data .= ", email = '$email' ";
        $data .= ", contact = '$contact' ";
        $data .= ", about_content = '" . htmlentities(str_replace("'", "&#x2019;", $about)) . "' ";
        if ($_FILES['img']['tmp_name'] != '') {
            $fname = strtotime(date('y-m-d H:i')) . '_' . $_FILES['img']['name'];
            $move = move_uploaded_file($_FILES['img']['tmp_name'], 'assets/uploads/' . $fname);
            $data .= ", cover_img = '$fname' ";

        }

        // echo "INSERT INTO system_settings set ".$data;
        $chk = $this->db->query("SELECT * FROM system_settings");
        if ($chk->num_rows > 0) {
            $save = $this->db->query("UPDATE system_settings set " . $data);
        } else {
            $save = $this->db->query("INSERT INTO system_settings set " . $data);
        }
        if ($save) {
            $query = $this->db->query("SELECT * FROM system_settings limit 1")->fetch_array();
            foreach ($query as $key => $value) {
                if (!is_numeric($key)) {
                    $_SESSION['system'][$key] = $value;
                }

            }

            return 1;
        }
    }

    public function save_category()
    {
        extract($_POST);
        $data = " name = '$name' ";
        $data .= ", price = $price ";

        if (empty($id)) {
            $save = $this->db->query("INSERT INTO categories set $data");
            if($save){
                $id = $this->db->insert_id;
                $newCategory = $this->db->query("SELECT * FROM categories where id = '$id'");
                $newCategory = mysqli_fetch_object($newCategory);
            }
            $this->audit(COMPONENT_CATEGORY,AUDIT_ACTION_CREATE,NULL,json_encode($newCategory));
        } else {
            $oldCategory = $this->db->query("SELECT * FROM categories where id = '$id'");
            $oldCategory = mysqli_fetch_object($oldCategory);
            $save = $this->db->query("UPDATE categories set $data where id = $id");
            $newCategory = $this->db->query("SELECT * FROM categories where id = '$id'");
            $newCategory = mysqli_fetch_object($newCategory);
            $this->audit(COMPONENT_CATEGORY,AUDIT_ACTION_UPDATE,json_encode($oldCategory),json_encode($newCategory));
        }
        if ($save) {
            return 1;
        }

    }
    public function delete_category()
    {
        extract($_POST);
        $existScooters = $this->db->query("SELECT count(*) as total FROM scooters where categoryId = " . $id);
        $existScooters = mysqli_fetch_object($existScooters);
        if($existScooters->total > 0){
            return 2;
        }
        $oldCategory = $this->db->query("SELECT * FROM categories where id = '$id'");
        $oldCategory = mysqli_fetch_object($oldCategory);
        $delete = $this->db->query("DELETE FROM categories where id = " . $id);
        if ($delete) {
            $this->audit(COMPONENT_CATEGORY,AUDIT_ACTION_DELETE,json_encode($oldCategory),NULL);
            return 1;
        }
    }
    public function save_scooter()
    {
        extract($_POST);
        $data = " scooterNumber = '$scooterNumber' ";
        $data .= ", categoryId = '$categoryId' ";
        $data .= ", price = '$price' ";
        $data .= ", stateId = '$stateId' ";
        if(intval($partnerId) == 0){
            $partnerId = 'null';
        }
        if(!empty($description)) $data .= ", description = '$description' ";

        $data .= ", partnerId = $partnerId ";  

        foreach($_POST as $k => $v){
            if(strpos($k,'field') !== false){
                $data .= " , $k = '$v'";
            }
        }

        if (empty($id)) {
            $chk = $this->db->query("SELECT * FROM scooters where scooterNumber = '$scooterNumber' ")->num_rows;
            if ($chk > 0) {
                return 2;
                exit;
            }
            $save = $this->db->query("INSERT INTO scooters set $data");
            if($save){
                $id = $this->db->insert_id;
                $newScooter = $this->db->query("SELECT * FROM scooters where id = '$id'");
                $newScooter = mysqli_fetch_object($newScooter);
                $this->scooter_audit($id,AUDIT_ACTION_CREATE,null,json_encode($newScooter));
            }
        } else {
            $chk = $this->db->query("SELECT * FROM scooters where scooterNumber = '$scooterNumber' and id <> $id ")->num_rows;
            if ($chk > 0) {
                return 2;
                exit;
            }
            //Obtengo el scooter antes de modificarse y despues para guardar en la auditoria
            $oldScooter = $this->db->query("SELECT * FROM scooters where id = '$id'");
            $oldScooter = mysqli_fetch_object($oldScooter);
            $save = $this->db->query("UPDATE scooters set $data where id = $id");
            $newScooter = $this->db->query("SELECT * FROM scooters where id = '$id'");
            $newScooter = mysqli_fetch_object($newScooter);
            $this->scooter_audit($id,AUDIT_ACTION_UPDATE,json_encode($oldScooter), json_encode($newScooter));
        }
        if ($save) {
            return 1;
        }

    }
    public function delete_scooter()
    {
        extract($_POST);

        $existTenant = $this->db->query("SELECT count(*) as total FROM tenants where scooterId = " . $id);
        $existTenant = mysqli_fetch_object($existTenant);
        if($existTenant->total > 0){
            return 2;
        } 

        $delete = $this->db->query("DELETE FROM scooters where id = " . $id);
        $this->scooter_audit($id,AUDIT_ACTION_DELETE,null,null);
        if ($delete) {
            return 1;
        }
    }

    public function  deliver_scooter(){
        extract($_POST);
        $oldScooter = $this->db->query("SELECT * FROM scooters where id = '$scooterId'");
        $oldScooter = mysqli_fetch_object($oldScooter);
        
        $state = STATE_OCCUPIED;    
        $state = $this->db->query("SELECT id FROM states where name = '$state' ");
        $state = mysqli_fetch_object($state);

        if($state){
            $data = " stateId = $state->id, partnerId = null ";
            $this->db->query("START TRANSACTION");
            $save = $this->db->query("UPDATE scooters set $data where id = $scooterId");
            if($save){
                $newScooter = $this->db->query("SELECT * FROM scooters where id = '$scooterId'");
                $newScooter = mysqli_fetch_object($newScooter);
                $this->scooter_audit($scooterId,AUDIT_ACTION_UPDATE,json_encode($oldScooter), json_encode($newScooter));
                if($this->save_payment_swap($id)){
                    $this->db->query("COMMIT");
                    return 1;
                }
            }
        }
        $this->db->query("ROLLBACK");
        return 0;   
    }

    public function save_payment_swap($id)
    {
        $user = intval($_SESSION['login_id']);
        $swapAmount = $this->get_swap_amount($user);
        if($swapAmount > 0){
            $data = "swap_amount = $swapAmount";
            $data = "amount = $swapAmount";
            $data .= " ,partnerId = $user";
            $data .= " ,tenantId = $id";
            $data .= " ,invoice = 'swap'";
            $finalAmount = $swapAmount * -1;
            $data .= " , final_amount = $finalAmount";
            $data .= " , partner_amount = $swapAmount";
            return $this->db->query("INSERT INTO payments set $data");
        }
        return true;
    }
    
    public function save_tenant()
    {
        extract($_POST);

        $userType = intval($_SESSION['login_type']);
        $user = intval($_SESSION['login_id']);
        
        if($userType !== ID_TYPE_PARTNER && $partnerId != '-1'){
            $data = " partnerId = $partnerId ";  
        }elseif($userType == ID_TYPE_PARTNER && empty($id)){
            $data = " partnerId = $user ";  
        }

        $this->db->query("START TRANSACTION");
        if (empty($id)) {        
            isset($data) ? $data .= ", firstName = '$firstName' " :  $data = " firstName = '$firstName' ";
            $data .= ", lastName = '$lastName' ";
            $data .= ", middleName = '$middleName' ";
            $data .= ", email = '$email' ";
            $data .= ", contact = '$contact' ";
            $data .= ", scooterId = '$scooterId' ";
            $data .= ", dateIn = '$dateIn' ";
            $data .= ", price = '$price' ";
            $save = $this->db->query("INSERT INTO tenants set $data");
            //Si se guardo con exito el alquiler, actualizo el estado del patinete a ocupado
            if($save){
                $id = $this->db->insert_id;
                $newTenant = $this->db->query("SELECT * FROM tenants where id = '$id'");
                $newTenant = mysqli_fetch_object($newTenant);
                $this->audit(COMPONENT_TENANT,AUDIT_ACTION_CREATE,NULL,json_encode($newTenant));
            }
        } else {
            $oldScooter = $this->db->query("SELECT scooterId as id, partnerId FROM tenants where id = '$id' ");
            $oldScooter = mysqli_fetch_object($oldScooter);
            if($userType == ID_TYPE_PARTNER){
                if($scooterId !== $oldScooter->id){
                    if(!$this->save_payment_swap($id)){
                        $this->db->query("ROLLBACK");
                        return 0;
                    }
                }
                isset($data) ? $data .= ", scooterId = '$scooterId' " :  $data = " scooterId = '$scooterId' ";
            }else{
                isset($data) ? $data .= ", firstName = '$firstName' " :  $data = " firstName = '$firstName' ";
                $data .= ", lastName = '$lastName' ";
                $data .= ", middleName = '$middleName' ";
                $data .= ", email = '$email' ";
                $data .= ", contact = '$contact' ";
                $data .= ", scooterId = '$scooterId' ";
                $data .= ", dateIn = '$dateIn' ";
            }

            $oldTenant = $this->db->query("SELECT * FROM tenants where id = '$id'");
            $oldTenant = mysqli_fetch_object($oldTenant);

            $save = $this->db->query("UPDATE tenants set $data where id = $id");   
            $newTenant = $this->db->query("SELECT * FROM tenants where id = '$id'");
            $newTenant = mysqli_fetch_object($newTenant);

            $this->audit(COMPONENT_TENANT,AUDIT_ACTION_UPDATE,json_encode($oldTenant),json_encode($newTenant));         
        }
        if($id && $save){
            if(isset($partnerId) && $partnerId == '-1'){
                $state = STATE_RESERVED;
                $data = null;
            }else{
                $state = STATE_OCCUPIED;
                $data = " partnerId = null ";
            }
            //Actualizo el estado del patinete
            $state = $this->db->query("SELECT id FROM states where name = '$state' ");
            $state = mysqli_fetch_object($state);
            if($state !== null){
                isset($data) ? $data .= ", stateId = '$state->id' " :  $data = " stateId = '$state->id' ";
                $save = $this->db->query("UPDATE scooters set " . $data . " where id = " . $scooterId);
                if(!$save){
                    $this->db->query("ROLLBACK");
                    return 0;
                }

                if(isset($oldScooter->id) && $oldScooter->id !== $scooterId){
                    $stateFree = STATE_FREE;
                    $freeId = $this->db->query("SELECT id FROM states where name = '$stateFree' ");
                    $freeId = mysqli_fetch_object($freeId);
                    if($freeId != null){
                        $data = " stateId = $freeId->id ";
                        
                        if($userType !== ID_TYPE_PARTNER && $partnerId !== '-1'){
                            $data .= ", partnerId = $partnerId ";  
                        }elseif($userType == ID_TYPE_PARTNER){
                            $data .= ", partnerId = $user ";  
                        }
                        $save = $this->db->query("UPDATE scooters set " . $data . " where id = " . $oldScooter->id);
                        if(!$save){
                            $this->db->query("ROLLBACK");
                            return 0; 
                        }
                    }else{
                        $this->db->query("ROLLBACK");
                        return 0;
                    }                  
                }
                if(isset($_FILES['upload'])){
                    $totalCount = count($_FILES['upload']['name']);
                    for( $i=0 ; $i < $totalCount ; $i++ ) {
                        $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
                        $ext = pathinfo( $_FILES["upload"]["name"][$i], PATHINFO_EXTENSION ); 
                        $basename = str_replace('.'.$ext,'',$_FILES["upload"]["name"][$i]);
                        $filename = $id . "_" . $basename . "." . $ext;
                        if ($tmpFilePath != ""){
                            $newFilePath = __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $filename;
                            move_uploaded_file($tmpFilePath, $newFilePath);
                        }
                    }    
                }    
                $this->db->query("COMMIT");
                return 1;    
            }
        }
        $this->db->query("ROLLBACK");
        return 0;    
    }

    public function change_tenant_status(){
        extract($_POST);

        $this->db->query("START TRANSACTION");
        $oldTenant = $this->db->query("SELECT * FROM tenants where id = '$id'");
        $oldTenant = mysqli_fetch_object($oldTenant);
        $data = " status = $status";
        if(isset($_POST['dateFinish'])){
            $data .= " , dateFinish = '$dateFinish'";
        }
        $update = $this->db->query("UPDATE tenants set $data where id = " . $id);
        $newTenant = $this->db->query("SELECT * FROM tenants where id = '$id'");
        $newTenant = mysqli_fetch_object($newTenant);
        if($update){
            $this->audit(COMPONENT_TENANT,AUDIT_ACTION_UPDATE,json_encode($oldTenant), json_encode($newTenant));  
            if($this->release_scooter($oldTenant->partnerId, $scooterId)){
                $this->db->query("COMMIT");
                return 1; 
            }else{
                $this->db->query("ROLLBACK");
                return 0; 
            }
        }
        $this->db->query("ROLLBACK");
        return 0; 
    }

    public function release_scooter($partnerId,  $scooterId)
    {
        $userType = intval($_SESSION['login_type']);
        $userLogin = intval($_SESSION['login_id']);

        $stateFree = STATE_FREE;
        $freeId = $this->db->query("SELECT id FROM states where name = '$stateFree' ");
        $freeId = mysqli_fetch_object($freeId);
        $data = " stateId = $freeId->id ";

        if($userType == ID_TYPE_PARTNER){
            $data .= " , partnerId = $userLogin ";
        }
        
        $save = $this->db->query("UPDATE scooters set " . $data . " where id = " . $scooterId);
        return $save;
    }

    public function get_tdetails()
    {
        extract($_POST);
        $data = array();
        $tenants = $this->db->query("SELECT t.*,concat(t.lastName,', ',t.firstName,' ',t.middleName) as name,s.scooterNumber,s.price FROM tenants t inner join scooters s on s.id = t.scooterId where t.id = {$id} ");
        foreach ($tenants->fetch_array() as $k => $v) {
            if (!is_numeric($k)) {
                $$k = $v;
            }
        }

        $startDate = new DateTime($dateIn." 00:00:00");
        $dateFinish = $dateFinish ? $dateFinish . '23:59:59' : date('Y-m-d')." 23:59:59";
        $endDate = new DateTime($dateFinish); 
        $interval = $endDate->diff($startDate);
        $months = $interval->format('%m');
        $months = $months + 1;
        $data['months'] = $months;
        $payable = $price * $months;
        $data['payable'] = $payable;

        $paid = $this->db->query("SELECT SUM(amount) as paid FROM payments where id != '$pid' and tenantId =" . $id." AND invoice != 'swap' AND status = 1 ");
        $last_payment = $this->db->query("SELECT * FROM payments where id != '$pid' and tenantId =" . $id . " AND invoice != 'swap' AND status = 1 order by unix_timestamp(dateCreated) desc limit 1");
        $paid = $paid->num_rows > 0 ? $paid->fetch_array()['paid'] : 0;
        $data['paid'] = number_format($paid, 2);
        $data['last_payment'] = $last_payment->num_rows > 0 ? date("M d, Y", strtotime($last_payment->fetch_array()['dateCreated'])) : 'N/A';
        $data['outstanding'] = number_format($payable - $paid, 2);
        $data['price'] = number_format($price, 2);
        $data['name'] = ucwords($name);
        $data['rent_started'] = date('M d, Y', strtotime($dateIn));

        return json_encode($data);
    }

    public function confirm_invoice(){
        extract($_POST);
        $this->db->query("START TRANSACTION");
        $date = date('Y-m-d H:i:s');
        $data = ' status = 1';
        $oldPayment = $this->db->query("SELECT * FROM payments where id = '$id'");
        $oldPayment = mysqli_fetch_object($oldPayment);        

        $save = $this->db->query("UPDATE payments set $data where id = $id");
        if($save){
            $newPayment = $this->db->query("SELECT * FROM payments where id = '$id'");
            $newPayment = mysqli_fetch_object($newPayment);
            $save = $this->audit(COMPONENT_PAYMENT,AUDIT_ACTION_UPDATE,json_encode($newPayment),json_encode($oldPayment));
            if($save){
                $this->db->query("COMMIT");
                return 1;
            }
        }
        $this->db->query("ROLLBACK");
        return 0;
    }
    public function save_payment()
    {
        extract($_POST);

        $data = "";
        foreach ($_POST as $k => $v) {
            if (!in_array($k, array('id', 'ref_code')) && !is_numeric($k)) {
                if(empty($v)) continue;
                if (empty($data)) {
                    $data .= " $k='$v' ";
                } else {
                    $data .= ", $k='$v' ";
                }
            }
        }

        $partner = $this->db->query("SELECT partnerId as id, dateIn FROM tenants where id = '$tenantId'");
        $partner = mysqli_fetch_object($partner);
        if(isset($partner->id) && $partner->id !== null && $partner->dateIn !== null){
            $partnerId = $partner->id;
            $dateIn = $partner->dateIn;
            $ivaAmount = $this->get_iva_amount($partnerId, $amount);
            $data .= " , iva_amount = $ivaAmount";
    
            
            if(empty($dateCreated)){
                $dateCreated = date('Y-m-d');
            }
            $monthlyAmount = $this->get_monthly_amount($partnerId,$dateIn,$dateCreated);
            $data .= " , monthly_amount = $monthlyAmount";
    
            $finalAmount = (float)round($amount - $ivaAmount - $monthlyAmount,2);
            $partnerAmount = (float)round($ivaAmount +  $monthlyAmount,2);
            $data .= " , final_amount = $finalAmount";
            $data .= " , partner_amount = $partnerAmount";
            $data .= " , partnerId = $partnerId";
        }else{
            $data .= " , partnerId = null";
            $data .= " , final_amount = $amount";
        }
       
       

        if (empty($id)) {
            $save = $this->db->query("INSERT INTO payments set $data");
            if($save){
                $id = $this->db->insert_id;
                $newPayment = $this->db->query("SELECT * FROM payments where id = '$id'");
                $newPayment = mysqli_fetch_object($newPayment);
                $this->audit(COMPONENT_PAYMENT,AUDIT_ACTION_CREATE,NULL,json_encode($newPayment));
            }
        } else {
            $oldPayment = $this->db->query("SELECT * FROM payments where id = '$id'");
            $oldPayment = mysqli_fetch_object($oldPayment);        
            $save = $this->db->query("UPDATE payments set $data where id = $id");
            $newPayment = $this->db->query("SELECT * FROM payments where id = '$id'");
            $newPayment = mysqli_fetch_object($newPayment);
            $this->audit(COMPONENT_PAYMENT,AUDIT_ACTION_UPDATE,json_encode($newPayment),json_encode($oldPayment));
        }

        if ($save) {
            return 1;
        }
    }

    public function get_swap_amount($partnerId){
        $swapAmount = $this->db->query("SELECT amount FROM partner_payment_swap_rules where partnerId = '$partnerId'");
        $swapAmount = mysqli_fetch_object($swapAmount);
        return $swapAmount ? $swapAmount->amount : 0;
    }

    public function get_iva_amount($partnerId, $amount){
        $ivaPercentage = $this->db->query("SELECT percentage FROM partner_payment_iva_rules where partnerId = '$partnerId'");
        $ivaPercentage = mysqli_fetch_object($ivaPercentage);
        $commission = 0;
        if($ivaPercentage){
            $ivaAmount = round((float)($amount / 1.21),2);
            $amountWithoutIva = $amount - $ivaAmount;
            $commission = round((float)($ivaPercentage->percentage * $amountWithoutIva) / 100, 2);
        }
        return $commission;
    }

    public function get_monthly_amount($partnerId,$dateIn,$date){

        $monthlyAmount = $this->db->query("SELECT amount, months FROM partner_payment_monthly_rules where partnerId = '$partnerId'");
        $monthlyAmount = mysqli_fetch_object($monthlyAmount);
        $commission = 0;
        if($monthlyAmount){

            //A los meses seteados le resto 1 porque el mes en que se registra el alquiler cuenta como comision
            $monthlyAmount->months = $monthlyAmount->months - 1;

            //Obtengo el primer dia del mes de la fecha de pago
            $startDate = date('Y-m-01', strtotime($date));

            //Le sumo la cantidad de meses correspondientes a la fecha de creacion de alquiler
            //Para verificar si esta dentro de la fecha de pago
            $endDate = date('Y-m-t', strtotime($dateIn."+ ".$monthlyAmount->months." month"));

            //Si la fecha de pago es menor a la fecha limite de comision la cobra
            if(strtotime($startDate) <= strtotime($endDate) ){
                $commission = $monthlyAmount->amount;
            }
            
        }
        return $commission;
    }

    public function delete_payment()
    {
        extract($_POST);
        $oldPayment = $this->db->query("SELECT * FROM payments where id = '$id'");
        $oldPayment = mysqli_fetch_object($oldPayment);
        $delete = $this->db->query("DELETE FROM payments where id = " . $id);
       if ($delete) {
            $this->audit(COMPONENT_PAYMENT,AUDIT_ACTION_DELETE,json_encode($oldPayment),NULL);
            return 1;
        }
    }
    public function save_state()
    {
        extract($_POST);
        $date = date('Y-m-d H:i:s');
        $username =  $_SESSION['login_username'];
       
        $data = " name = '$name' ";
        $data .= ", description = '$description' ";
        $sql = "SELECT * FROM states where name = '$name'";
        $sql .= $id ? " AND id <> $id " : "";
        $existState= $this->db->query($sql);

        if($existState->num_rows > 0){
            return 1; 
        }
        
        if (empty($id)) {
            $data .= ", createdAd = '$date' ";
            $data .= ", createdBy = '$username' ";
            $data .= ", updatedBy = '$username' ";
            $save = $this->db->query("INSERT INTO states set $data");
            if($save){
                $id = $this->db->insert_id;
                $newState = $this->db->query("SELECT * FROM states where id = '$id'");
                $newState = mysqli_fetch_object($newState);
            }
            $this->audit(COMPONENT_STATE,AUDIT_ACTION_CREATE,NULL,json_encode($newState));
            return 2;
        } else {
            $oldState = $this->db->query("SELECT * FROM states where id = '$id'");
            $oldState = mysqli_fetch_object($oldState);
            $data .= ", updatedAt = '$date' ";
            $data .= ", updatedBy = '$username' ";
            $save = $this->db->query("UPDATE states set $data where id = $id");
            $newState = $this->db->query("SELECT * FROM states where id = '$id'");
            $newState = mysqli_fetch_object($newState);
            $this->audit(COMPONENT_STATE,AUDIT_ACTION_UPDATE,json_encode($newState),json_encode($oldState));
            return 3;
        }

    }
    public function delete_state()
    {
        extract($_POST);

        $existScooters = $this->db->query("SELECT count(*) as total FROM scooters where categoryId = " . $id);
        $existScooters = mysqli_fetch_object($existScooters);
        if($existScooters->total > 0){
            return 2;
        } 

        $oldState = $this->db->query("SELECT * FROM states where id = '$id'");
        $oldState = mysqli_fetch_object($oldState);
        $delete = $this->db->query("DELETE FROM states where id = " . $id);
        if ($delete) {
            $this->audit(COMPONENT_STATE,AUDIT_ACTION_DELETE,json_encode($oldState),NULL);
            return 1;
        }
    }

    public function save_incident()
    { 
        extract($_POST);
        $date = date('Y-m-d H:i:s');
        $username =  $_SESSION['login_username'];
        if (!empty($scooterId)) {
            //Obtengo el numero del scooter
            $scooter = $this->db->query("SELECT scooterNumber FROM scooters where id = '$scooterId'");
            $scooter = mysqli_fetch_object($scooter);

            $data = " createdAd = '$date' ";
            $data .= ", createdBy = '$username' ";
            $data .= ", updatedAt = '$date' ";
            $data .= ", updatedBy = '$username' ";
            $data .= ", scooterId = '$scooterId' ";
            $data .= ", description = '$description' ";
            $data .= ", scooterNumber = '$scooter->scooterNumber' ";
            //Creo la incidencia
            $save = $this->db->query("INSERT INTO scooter_incidents set $data");
            if($save){
                return 1;
            } 
        }
        return 2;
    }

    public function resolve_incident()
    { 
        extract($_POST);
        $incidents = json_decode($incidents);
        if(count($incidents) > 0){
            $date = date('Y-m-d H:i:s');
            $username =  $_SESSION['login_username'];
            $this->db->query("START TRANSACTION");
            foreach ($incidents as $incident) {
                $data = " updatedAt = '$date' ";
                $data .= ", updatedBy = '$username' ";
                $data .= ", resolved = '$incident->resolve' ";
                $save = $this->db->query("UPDATE scooter_incidents set " . $data . " where id = " . $incident->id);
                if(!$save){
                    $this->db->query("ROLLBACK");
                    return 2;
                }
            }
            $this->db->query("COMMIT");
        }else{
            return 3;
        }
        return 1;
        
    }

    public function scooter_audit($scooterId, $action, $oldObject, $newObject){
        $date = date('Y-m-d H:i:s');
        $username =  $_SESSION['login_username'];
        $data = " scooterId = '$scooterId' ";
        $data .= ", date = '$date' ";
        $data .= ", username = '$username' ";
        $data .= ", action = '$action' ";
        if($oldObject){
            $data .= ", oldObject = '$oldObject' ";
        }
        if($newObject){
            $data .= ", newObject = '$newObject' ";
        }
        return $this->db->query("INSERT INTO scooter_audits set $data");
    }

    public function audit($component, $action, $oldObject, $newObject){
        $date = date('Y-m-d H:i:s');
        $username =  $_SESSION['login_username'];
        $data = " component = '$component' ";
        $data .= ", date = '$date' ";
        $data .= ", username = '$username' ";
        $data .= ", action = '$action' ";
        if($oldObject){
            $data .= ", oldObject = '$oldObject' ";
        }
        if($newObject){
            $data .= ", newObject = '$newObject' ";
        }
        return $this->db->query("INSERT INTO audits set $data");
    }

}

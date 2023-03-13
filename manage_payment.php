<?php 
include 'db_connect.php'; 
include 'constants.php'; 
$type = 0;
$userId = 0;
if(isset($_GET['type']) && isset($_GET['userId'])){
	$type = $_GET['type'];
	$userId = $_GET['userId'];
}
if(isset($_GET['id'])){
$qry = $conn->query("SELECT * FROM payments where id= ".$_GET['id']);
foreach($qry->fetch_array() as $k => $val){
    $$k=$val;
}
}
?>
<div class="container-fluid">
    <form action="" id="manage-payment">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div id="msg"></div>
        <div class="form-group">
            <label for="" class="control-label">Cliente</label>
            <select name="tenantId" id="tenantId" class="custom-select select2"  required="required">
                <option value=""></option>

            <?php 
            $query = "SELECT *,concat(lastName,', ',firstName,' ',middleName) as name FROM tenants where status = 1";
            $query .= intval($type) == ID_TYPE_PARTNER ? ' AND partnerId ='.intval($userId) : '';
            $query .= " order by name asc";
            $tenant = $conn->query($query);
            while($row=$tenant->fetch_assoc()):
            ?>
            <option value="<?php echo $row['id'] ?>" <?php echo isset($tenantId) && $tenantId == $row['id'] ? 'selected' : '' ?>><?php echo ucwords($row['name']) ?></option>
            <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group" id="details">
            
        </div>

        <div class="form-group">
            <label for="" class="control-label">Factura: </label>
            <input type="text" class="form-control" name="invoice"  value="<?php echo isset($invoice) ? $invoice :'' ?>" >
        </div>
        <div class="form-group">
            <label for="" class="control-label">Monto pagado: </label>
            <input type="number" class="form-control text-right" step="any" name="amount"  value="<?php echo isset($amount) ? $amount :'' ?>" required >
        </div>
        <div class="form-group">
            <label for="" class="control-label">Fecha de pago</label>
            <input type="date" class="form-control" name="dateCreated" value="<?php echo isset($dateCreated) ? date("Y-m-d",strtotime($dateCreated)) :'' ?>">
            <span style="font-size: 15px;">Solo para pagos retroactivos</span>
        </div>
</div>
    </form>
</div>
<div id="details_clone" style="display: none">
    <div class='d'>
        <large><b>Detalle</b></large>
        <hr>
        <p>Cliente: <b class="tname"></b></p>
        <p>Tarifa de alquiler mensual: <b class="price"></b></p>
        <p>Saldo pendiente: <b class="outstanding"></b></p>
        <p>Total pagado: <b class="total_paid"></b></p>
        <p>Rent iniciada: <b class='rent_started'></b></p>
        <p>Meses a pagar: <b class="payable_months"></b></p>
        <hr>
    </div>
</div>
<script>
    $(document).ready(function(){
        if('<?php echo isset($id)? 1:0 ?>' == 1)
             $('#tenantId').trigger('change') 
    })
   $('.select2').select2({
    placeholder:"Por favor seleccionar",
    width:"100%"
   })
   $('#tenantId').change(function(){
    if($(this).val() <= 0)
        return false;

    start_load()
    $.ajax({
        url:'ajax.php?action=get_tdetails',
        method:'POST',
        data:{id:$(this).val(),pid:'<?php echo isset($id) ? $id : '' ?>'},
        success:function(resp){
            if(resp){
                resp = JSON.parse(resp)
                var details = $('#details_clone .d').clone()
                details.find('.tname').text(resp.name)
                details.find('.price').text(resp.price)
                details.find('.outstanding').text(resp.outstanding)
                details.find('.total_paid').text(resp.paid)
                details.find('.rent_started').text(resp.rent_started)
                details.find('.payable_months').text(resp.months)
                $('#details').html(details)
            }
        },
        complete:function(){
            end_load()
        }
    })
   })
    $('#manage-payment').submit(function(e){
        if(!$('#manage-payment')[0].checkValidity()){
			$('#manage-payment')[0].reportValidity()
			return false;
		}
        e.preventDefault()
        start_load()
        $('#msg').html('')
        $.ajax({
            url:'ajax.php?action=save_payment',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp==1){
                    alert_toast("Se creo el pago con exito.",'success')
                        setTimeout(function(){
                            location.reload()
                        },1000)
                }
            }
        })
    })
</script>
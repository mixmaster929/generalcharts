<?php 
include('db_connect.php');
session_start();
if(isset($_GET['id'])){
$user = $conn->query("SELECT u.*, per.percentage AS ivaPercentage, per.id as ivaId, pmr.amount as monthlyAmount, pmr.months, pmr.id as monthlyId, psr.id as swapId, psr.amount as swapAmount FROM users AS u LEFT OUTER JOIN partner_payment_iva_rules AS per ON per.partnerId = u.id LEFT OUTER JOIN partner_payment_monthly_rules AS pmr ON pmr.partnerId = u.id LEFT OUTER JOIN partner_payment_swap_rules AS psr ON psr.partnerId = u.id WHERE u.id =".$_GET['id']);
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
}
?>
<div class="container-fluid">
	<div id="msg"></div>
	
	<form action="" id="manage-user">	
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="row form-group">
			<div class="col-md-6">
				<label for="name">Nombre</label>
				<input type="text" name="name" id="name" class="form-control" value="<?php echo isset($meta['name']) ? $meta['name']: '' ?>" required>
			</div>
			<div class="col-md-6">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required  autocomplete="off">
			</div>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="form-control" value="" <?php echo !isset($meta['id']) ? 'required': '' ?>  autocomplete="off">
			<?php if(isset($meta['id'])): ?>
			<small><i>Deje esto en blanco si no desea cambiar la contraseña.</i></small>
		<?php endif; ?>
		</div>
		<?php if(isset($meta['type']) && $meta['type'] == 3): ?>
			<input type="hidden" name="type" value="3">
		<?php else: ?>
		<?php if(!isset($_GET['mtype'])): ?>
		<div class="form-group">
			<label for="type">Tipo de usuario</label>
			<select name="type" id="type" class="custom-select" required>
				<option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected': '' ?>>Partner</option>
				<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected': '' ?>>Admin</option>
			</select>
		</div>
		<div id='partner_payment_rules' style='display:none;'>
			<div class="form-group">
				<label for="type">Regla mensual:</label>
				<small><input data-id = "<?php echo(isset($meta['monthlyId']) ? $meta['monthlyId'] : 0)?>" id="monthly_rule" type="checkbox" <?php echo(isset($meta['monthlyId']) ? 'checked="checked"' : '')?> /></small>
			</div>
			<div id="monthly_rule_inputs" style='display:none;'>
				<div class="row form-group">
				<div class="col-md-6">
						<label for="type">Monto:</label>
						<input id="monthly_rule_amount" type="number" value="<?php echo(isset($meta['monthlyId']) && $meta['monthlyAmount'] ? $meta['monthlyAmount'] : '')?>" />
					</div>
					<div class="col-md-6">
						<label for="type">Meses:</label>
						<input id="monthly_rule_months" type="number" value="<?php echo(isset($meta['monthlyId']) && $meta['months'] ? $meta['months'] : '')?>" />
					</div>
				</div>
			</div>
			<div class="form-group">
					<label for="type">Regla IVA:</label>
					<small><input data-id ="<?php echo(isset($meta['ivaId']) ? $meta['ivaId'] : 0)?>" id="iva_rule" type="checkbox" <?php echo(isset($meta['ivaId']) ? 'checked="checked"' : '')?> /></small>
			</div>
			<div id="iva_rule_inputs" style='display:none;'>
				<div class="form-group">
					<label for="type">Porcentaje:</label>
					<input id="iva_rule_percentage" type="number" value="<?php echo(isset($meta['ivaId']) && $meta['ivaPercentage'] ? $meta['ivaPercentage'] : '')?>" />
				</div>
			</div>
			<div class="form-group">
					<label for="type">Regla intercambio:</label>
					<small><input data-id ="<?php echo(isset($meta['swapId']) ? $meta['swapId'] : 0)?>" id="swap_rule" type="checkbox" <?php echo(isset($meta['swapId']) ? 'checked="checked"' : '')?> /></small>
			</div>
			<div id="swap_rule_inputs" style='display:none;'>
				<div class="form-group">
					<label for="type">Monto:</label>
					<input id="swap_rule_amount" type="number" value="<?php echo(isset($meta['swapId']) && $meta['swapAmount'] ? $meta['swapAmount'] : '')?>" />
				</div>
			</div>
		</div>
		<?php endif; ?>
		<?php endif; ?>
		

	</form>
</div>
<script>
	
	$( document ).ready(function() {
		const ETENER_IVA = 'iva_rule';
		const MONTHLY_IVA = 'monthly_rule'
		const SWAP_IVA = 'swap_rule'

		$('#type').trigger("change");

		const validateCheckMonthlyRule = function(e){
			if(!$(e).is(':checked')){
				$('#monthly_rule_months').val('');
				$('#monthly_rule_amount').val('');
				$('#monthly_rule_months').prop('required',false);
				$('#monthly_rule_amount').prop('required',false);
				$('#monthly_rule_inputs').hide();
			}else{
				$('#monthly_rule_inputs').show();
				$('#monthly_rule_months').prop('required',true);
				$('#monthly_rule_amount').prop('required',true);
			}
		}

		const validateCheckEternalRule = function(e){
			if(!$(e).is(':checked')){
				$('#iva_rule_percentage').val('');
				$('#iva_rule_percentage').prop('required',false);
				$('#iva_rule_inputs').hide();
			}else{
				$('#iva_rule_inputs').show();
				$('#iva_rule_percentage').prop('required',true);
			}
		}

		const validateCheckSwapRule = function(e){
			if(!$(e).is(':checked')){
				$('#swap_rule_amount').val('');
				$('#swap_rule_inputs').hide();
				$('#swap_rule_amount').prop('required',false);
			}else{
				$('#swap_rule_inputs').show();
				$('#swap_rule_amount').prop('required',true);
			}
		}

		validateCheckMonthlyRule($('#monthly_rule'));
		validateCheckEternalRule($('#iva_rule'));
		validateCheckSwapRule($('#swap_rule'));

		$('#monthly_rule').click(function(e){ 
			validateCheckMonthlyRule(this);
		});

		$('#iva_rule').click(function(e){ 
			validateCheckEternalRule(this);
		});

		$('#swap_rule').click(function(e){ 
			validateCheckSwapRule(this);
		});

		$('#manage-user').submit(function(e){
			if(!$('#manage-user')[0].checkValidity()){
				$('#manage-user')[0].reportValidity()
				return false;
			}

			e.preventDefault();
			let formData = new FormData($(this)[0])
			var checkbox = $("#manage-user").find("input[type=checkbox]");
			let rules = new Array();

			$.each(checkbox, function(key, val) {
				let rule = {};
				rule.name = $(val).attr('id');
				rule.id = $(val).attr('data-id');
				rule.enable = $(val).is(':checked') ? 1 : 0;
				switch (rule.name.toLowerCase()) {
					case ETENER_IVA.toLowerCase():
						rule.percentage = $('#'+ETENER_IVA+'_percentage').val();
						break;
				
						case MONTHLY_IVA.toLowerCase():
						rule.amount = $('#'+MONTHLY_IVA+'_amount').val();
						rule.months = $('#'+MONTHLY_IVA+'_months').val();
						break;

						case SWAP_IVA.toLowerCase():
						rule.amount = $('#'+SWAP_IVA+'_amount').val();
						break;
				}
				rules.push(rule);
			});
			rules = JSON.stringify(rules);
			formData.append('rules',rules);
			start_load()
			$.ajax({
				url:'ajax.php?action=save_user',
				method:'POST',
				data:formData,
				processData: false,
				contentType: false,
				success:function(resp){
					if(resp ==1){
						alert_toast("Data successfully saved",'success')
						setTimeout(function(){
							location.reload()
						},1500)
					}else if(resp==2){
						$('#msg').html('<div class="alert alert-danger">Username already exist</div>')
						end_load()
					}else if(resp==1){
						$('#msg').html('<div class="alert alert-danger">Ocurrio un error al crear el usuario</div>')
						end_load()
					}
				}
			})
		})

    });

	$('#type').change(function(){
			if($(this).val() <= 0)
				return false;
			const type = $(this).find('option:selected').val();
			//Si es partner muestro las reglas
			if(type == 2){
				$('#partner_payment_rules').show();
			}else{
				$('#partner_payment_rules').hide();
			}
   	})

	

</script>
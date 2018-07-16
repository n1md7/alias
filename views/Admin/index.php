<div class="row justify-content-sm-center">
	<div class="col col-sm-12 text-center my-3">
		<h2 class="text-dark">
			Sign-In here
		</h2>
	</div>
</div>
<div class="row justify-content-sm-center">
	<div class="col col-sm-5 alert-area text-center my-0">
	</div>
</div>
<div class="row justify-content-sm-center my-3">
	<div class="col col-sm-5 text-center">
	<form method="post" id="js-login">
		<div class="form-group">
			<input autofocus="on" placeholder="Username" autocomplete="off" type="text" class="form-control" id="username">
		</div>
		<div class="form-group bmd-form-group"> <!-- manually specified -->
			<input type="Password" placeholder="Password" class="form-control" id="password">
		</div>
		<div class="form-group bmd-form-group"> <!-- manually specified -->
			<input type="submit" class="btn btn-success" value="sign in">
		</div>
	</form>
	</div>
</div>

<script>
	jQuery('#js-login').submit( function(){
		jQuery.ajax({
			url: $('body').data('url') + 'Admin/login',
			method: 'POST',
			data: {
				'action': 'login',
				'username': $('#username').val(),
				'password': $('#password').val()
			},
			beforeSend: function(){
				$('.alert-area').empty();
				$('.alert-area').append(`<i class="fa fa-spinner fa-spin fa-3x fa-fw text-info"></i>`);
			},
			success: function(response){
				if( undefined !== response.Error){
					$('.alert-area').empty();
					$('.alert-area').append(`
							<div class="animated fadeIn alert alert-danger">
							  <strong>Error!</strong> Wrong Credentials.
							</div>
						`);
					return false;
				}
				$('.alert-area').empty();
				$('.alert-area').append(`
					<div class="animated fadeIn alert alert-success">
					  <strong>Success!</strong> Correct Credentials.
					</div>
				`);
				setTimeout(function(){
					window.location.reload();
				},1500);
			},
			error: function(){
				swal('Error', 'Connection problem', 'error');
			}
		});
		return false;
	});
</script>
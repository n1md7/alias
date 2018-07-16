<div class="row justify-content-center mt-5">
	<div class="col col-md-6 text-center">
		<h1 class="lang">Edit <b class="text-danger">GamePad</b> constants</h1>
	</div>
</div>
<div class="row justify-content-center my-5">
	
	<div class="col col-md-9">
		<!--Accordion wrapper-->
		<div class="accordion" id="accordionEx" role="tablist" aria-multiselectable="false">
			<?php foreach ($viewmodel['lang'] as $lang_key => $lang_value): ?>
			<!-- Accordion card -->
			<div class="card">
				<!-- Card header -->
				<div class="card-header" role="tab" id="heading<?php echo $lang_key; ?>">
					<a data-toggle="collapse" href="#collapse<?php echo $lang_key; ?>" aria-expanded="false" aria-controls="collapse<?php echo $lang_key; ?>">
						<h5 class="mb-0">
						Language <b class="text-uppercase"><?php echo $lang_key; ?></b><i class="fa fa-angle-down rotate-icon"></i>
						</h5>
					</a>
				</div>
				<!-- Card body -->
				<div id="collapse<?php echo $lang_key; ?>" class="collapse show" role="tabpanel" aria-labelledby="heading<?php echo $lang_key; ?>" data-parent="#accordionEx" >
					<div class="card-body">
						<?php foreach ($lang_value as $word_key => $word_value): ?>
						<div class="row justify-content-center text-center js-new-translate-container">
							<!-- one word translation -->
							<div class="col col-sm-4"><?php echo $word_key; ?></div>
							<div class="col col-sm-5 js-translate-text" contenteditable="true" data-lang="<?php echo $lang_key;?>" data-key="<?php echo $word_key; ?>"><?php echo $word_value; ?></div>
							<div class="col col-sm-2 align-self-center">
								<button class="btn btn-primary m-auto w-100 js-save-word">SAVE</button>
							</div>
							<hr class="w-100">
							<!-- end one word translation -->
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<!-- Accordion card -->
			<?php endforeach; ?>
			
		</div>
		<!--/.Accordion wrapper-->
	</div>
</div>
<script>
jQuery('.js-new-translate-container')
	.find('button.js-save-word')
		.click(function(){
			var cWord = $(this).parent().parent().find('.js-translate-text'),
				self = this;
			jQuery.ajax({
				url: $('body').data('url') + 'Admin/editConstants',
				method: 'POST',
				dataType: 'JSON',
				data: {
					lang: cWord.data('lang'),
					key: cWord.data('key'),
					value: cWord.html()
				},
				beforeSend: function(){
					$(self).attr('disabled', 'true').html('<i class="fa fa-spinner fa-spin fa-3x fa-fw text-warning"></i> saving...')
				},
				success: function(response){
					if( undefined === response.Success ){
						swal('Error', response.Error, 'error');
						return;
					}
					setTimeout(function(){
						$(self).removeAttr('disabled').html('SAVE');
					},1000)
				},
				error: function(){
					swal('Error', 'Connection problem!', 'error');
				}
			});
});
</script>
<section class="mb-5 mt-3">
	<div class="row justify-content-sm-center">
		<div class="col col-sm-12 text-center">
			<h2>
				Add/Delete <b class="text-danger">Words & categories</b>
			</h2>
		</div>
	</div>

	<div class="row justify-content-sm-center categories-container">
		<div class="col col-12 text-center py-2 my-1">
			<h3>Categories</h3>
		</div>
		<div class="col col-sm-5 text-center py-2 js-category-jar">
			<?php foreach ($viewmodel['categories'] as $category): ?>
				<button class="animated fadeIn js-category-delete btn btn-sm btn-primary p-1 m-1" data-id="<?php echo $category['cat_id']; ?>"><?php echo $category['cat_name']; ?> <i class="fa fa-close"></i></button>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="row d-flex justify-content-sm-center">
		<div class="col col-sm-3 text-center py-2 my-1 clearfix">
			<input type="text" maxlength="16" placeholder="Category name" class="js-cat-name form-control float-right">
		</div>
		<div class="col col-sm-auto text-center py-2 my-1 clearfix pl-0">
			<button class="w-100 btn btn-success m-0 js-cat-save">Add</button>
		</div>
	</div>

	<script>
		
		var Category = {
			save: function(){
				if($('.js-cat-name').val().length < 4){
					swal('info', 'Please type minimum 4 letter', 'info');
					return;
				}
				jQuery.ajax({
					url: $('body').data('url') + 'Admin/wordsEdit',
					method: 'POST',
					data: {
						'action': 'categoryAdd',
						'name': $('.js-cat-name').val()
					},
					beforeSend: function(){
						$('.js-cat-save')
							.attr('disabled', 'true')
							.html('<i class="fa fa-spinner fa-spin fa-3x fa-fw text-warning"></i> saving...');
					},
					success: function(response){
						if( undefined !== response.Error){
							swal('Error', response.Error, 'error');
						}
						setTimeout(function(){
							$('.js-category-jar').append(`
								<button class="animated fadeIn js-category-delete btn btn-sm btn-primary p-1 m-1" data-id="${response.id}">${$('.js-cat-name').val()} <i class="fa fa-close"></i></button>
								`);
							$('.js-category-select').append(`<option data-id="${response.id}" value="${response.id}">${$('.js-cat-name').val()}</option>`)

							$('.js-cat-name').val('');
							$('.js-cat-save')
								.removeAttr('disabled')
								.html('Add');

						}, 1000);
					},
					error: function(){
						swal('Error', 'Connection problem', 'error');
					}
				});
			},
			delete: function(){
				var self = this;
				 swal({
			        title: 'Are You Sure',
			        text: 'If you remove it, you will lose all the words assigned to that category!',
			        icon: 'warning',
			        className: 'lang',
			        buttons: [
			        	'no',
			        	'yes'
			        ],
			        dangerMode: true
			    }).then((result) => {
			    	if(result){
			    		jQuery.ajax({
							url: $('body').data('url') + 'Admin/wordsEdit',
							method: 'POST',
							data: {
								'action': 'categoryRemove',
								'id': $(self).data('id')
							},
							beforeSend: function(){
							},
							success: function(response){
								if( undefined !== response.Error){
									swal('Error', response.Error, 'error');
								}
								$(self).removeClass('fadeIn').addClass('fadeOut');
								setTimeout(function(){
									$(self).remove();
									$('.js-category-select>option').each(function(){
										if($(this).data('id') == $(self).data('id')){
											$(this).remove();
											return;
										}
									});
								},1000);
							},
							error: function(){
								swal('Error', 'Connection problem', 'error');
							}
						});
			    	}
			    });
			}
		};

		jQuery('.js-cat-save').click(Category.save);
		jQuery('body').on('click', '.js-category-delete', Category.delete);

	</script>

</section>

<hr>

<!-- section add new word -->
<section class="my-5">
	<div class="row justify-content-sm-center">
		<div class="col col-12 text-center">
			<h2>Add new words here <i class="fa fa-plus"></i></h2>
			<p class="my-2">Select category name and language</p>
			<p class="my-1 mb-2"><kbd>Enter</kbd> triggers button click</p>
		</div>			
	</div>
	<div class="row justify-content-sm-center mb-1">
		<div class="col col-sm-3 text-center">
			<select class="js-language-select form-control js-selection">
				<option value="0">Choose Language</option>
				<option value="en">English</option>
				<option value="ka">Georgian</option>
				<option value="me">Megrelian</option>
			</select>
		</div>
	</div>
	<div class="row justify-content-sm-center">
		<div class="col col-sm-3 text-center">
			<select class="js-category-select form-control js-selection">
				<option value="0">Choose Category</option>
				<?php foreach ($viewmodel['categories'] as $category): ?>
					<option data-id="<?php echo $category['cat_id']; ?>" value="<?php echo $category['cat_id']; ?>"><?php echo $category['cat_name']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	
	<div class="row d-flex justify-content-sm-center">
		<div class="col col-sm-3 text-center py-2 my-1 clearfix">
			<input type="text" placeholder="Word(s)" class="js-word-name form-control float-right" title="Split with !@#$%^&*()_+,.;'\ `~<>">
		</div>
		<div class="col col-sm-auto text-center py-2 my-1 clearfix pl-0">
			<button class="w-100 btn btn-success m-0 js-word-save">Add</button>
		</div>
	</div>
	<div class="row justify-content-sm-center">
		<div class="col col-sm-12 text-center py-2 js-words-jar">
			
		</div>
	</div>

	<script>
		var Words = {
			loadByCategory: function(){
				jQuery.ajax({
					url: $('body').data('url') + 'Admin/wordsEdit',
					method: 'POST',
					data: {
						'action': 'wordsByCategory',
						'cat_id': $('.js-category-select').val(),
						'lang': $('.js-language-select').val()
					},
					beforeSend:function(){
						console.log('loading')
						$('.js-words-jar').empty();

						$('.js-words-jar').append(`
								<div class="js-tmp-spinner">
									<p class="text-dark">Loading...</p>
									<i class="fa fa-spinner fa-spin fa-3x fa-fw text-dark"></i>
								</div>
							`);
					},
					success: function(response){
						$('.js-words-jar').empty();
						if(undefined !== response.Error){
							swal('Error', response.Error, 'error');
							return;		
						}
						if(response.Words.length == 0){
							$('.js-words-jar').append(`<h3>No data</h3>`);
							return;
						}
						response.Words.forEach(function(word, index){
							$('.js-words-jar').append(`
								<button class="animated fadeIn js-words-delete btn btn-sm btn-primary p-1 m-1" data-id="${word.word_id}">${word.word} <i class="fa fa-close"></i></button>
							`);
						});
					},
					error: function(){
						swal('Error', 'Connection problem', 'error');
					}
				});
			},
			delete: function(){
				var self = this;
				 swal({
			        title: 'Are You Sure',
			        text: 'This word will disappear forever!',
			        icon: 'warning',
			        className: 'lang',
			        buttons: [
			        	'no',
			        	'yes'
			        ],
			        dangerMode: true
			    }).then((result) => {
			    	if(result){
			    		jQuery.ajax({
							url: $('body').data('url') + 'Admin/wordsEdit',
							method: 'POST',
							data: {
								'action': 'wordRemove',
								'id': $(self).data('id')
							},
							beforeSend: function(){
								$(self).removeClass('fadeIn').addClass('fadeOut');
							},
							success: function(response){
								if( undefined !== response.Error){
									swal('Error', response.Error, 'error');
									$(self).removeClass('fadeOut').addClass('fadeIn');
								}else{
									setTimeout(_ => {$(self).remove()},1000);
								}
							},
							error: function(){
								swal('Error', 'Connection problem', 'error');
							}
						});
			    	}
			    });
			},
			save: function(){
				if($('.js-word-name').val().length <= 2){
					swal('info', 'Please type minimum 2 letter', 'info');
					return;
				}
				//parse and validate words by language
				var splitWith = /[!@#$%^&*()_+,.;'"\ `~<>\–\/\[\]]/;
				var langFilter = /./;
				switch($('.js-language-select').val()){
					case 'en':
						langFilter = /^[a-zA-Z\-]+$/;
					break;
					case 'me': 
						langFilter = /^[ა-ჸ\-]+$/;
					break;
					case 'ka': 
						langFilter = /^[ა-ჰ\-]+$/;
					break;
				}

				var wordsArray = $('.js-word-name').val().split(new RegExp(splitWith));
				var resultForXHR = wordsArray.filter(word => new RegExp(langFilter,'g').test(word));

				if(resultForXHR.length === 0){
					swal('info', 'Please type valid characters for current language', 'info');
					return;
				}

				jQuery.ajax({
					url: $('body').data('url') + 'Admin/wordsEdit',
					method: 'POST',
					data: {
						'action': 'wordAdd',
						'cat_id': $('.js-category-select').val(),
						'words': resultForXHR,
						'lang': $('.js-language-select').val()
					},
					beforeSend: function(){
						$('.js-word-save')
							.attr('disabled', 'true')
							.html('<i class="fa fa-spinner fa-spin fa-3x fa-fw text-warning"></i> saving...');
					},
					success: function(response){
						if( undefined !== response.Error){
							swal('Error', response.Error, 'error');
							$('.js-word-name').val('');
							$('.js-word-save')
								.removeAttr('disabled')
								.html('Add');
							return;
						}
						if(response.ids){
							console.log(response.ids)
							setTimeout(function(){
								response.ids.forEach(function(id, i){
									$('.js-words-jar').prepend(`
										<button class="animated fadeIn js-words-delete btn btn-sm btn-primary p-1 m-1" data-id="${id}">${resultForXHR[i]} <i class="fa fa-close"></i></button>
										`);

									$('.js-word-name').val('');
									$('.js-word-save')
										.removeAttr('disabled')
										.html('Add');

								});

							}, 100);
							$('.js-word-name').trigger('focus');
						}else{
							swal('Error', 'Something wrong', 'error');
						}
					},
					error: function(){
						swal('Error', 'Connection problem', 'error');
					}
				});
			}
		};

		jQuery('body')
			.on('change', '.js-category-select, .js-language-select', function(){
				var loadWords = true;
				$('.js-selection').each(function(){
					if($(this).val() == 0) loadWords = false;
				});

				loadWords? Words.loadByCategory():false;
		})
		.on('click', '.js-words-delete', Words.delete);

		jQuery('.js-word-save').click(Words.save);

		// on enter or tab save
		$('.js-word-name').keypress(function(e) {
		    if(e.which == 13) {
		        Words.save();
		    }
		});

	</script>
</section>

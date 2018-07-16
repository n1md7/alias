var currentLang = 'en';
if( undefined === cookie.get("lang") ){
	cookie.set('lang', 'en', 999);
}else{
	currentLang = cookie.get('lang');
}

$('#lang_en').click(x => {
	cookie.set('lang', 'en', 999);
	$('#langChangeModal').modal('hide');
	currentLang = cookie.get('lang');
	translate();
	generateCategories();
});
$('#lang_ka').click(x => {
	cookie.set('lang', 'ka', 999);
	$('#langChangeModal').modal('hide');
	currentLang = cookie.get('lang');
	translate();
	generateCategories();
});

/*$("#langChangeModal").on('hidden.bs.modal', function(){
    translate();
});*/


var translate = function(){
	$('.lang').each(function(){
		for(var lang in langs){
			for(var text in langs[lang]){
				if($(this).hasClass(text) && currentLang == lang){
					$(this)[0].outerHTML.indexOf("/") != -1?
						$(this).html(langs[lang][text]):
						$(this).val(langs[lang][text]);
				}
				
			}
		}
	});
};

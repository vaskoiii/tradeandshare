jQuery(function($){
	// search both >> result either >> display both
	$('input[name=contact_user_mixed]').autoComplete({
		width: '200px',
		ajax: '/index.php?x=/autocontact_autouser_ajax/'
	});
	$('input[name=lock_contact_user_mixed]').autoComplete({
		width: '200px',
		ajax: '/index.php?x=/autocontact_autouser_ajax/'
	});
	$('input[name=page_name]').autoComplete({
		width: '200px',
		ajax: '/index.php?x=/autopage_ajax/'
	});
});

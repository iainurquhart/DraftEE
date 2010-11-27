function draftee_notify_drafts(count)
{
	
	if(count != 1) 
		var plural = 's';
	else
		var plural = '';
	
	var notice = '<p class="draftee_warning draftee_title_prefix">This entry has ' + count + ' draft' + plural + '.</p>';
	
	$('#hold_field_title').append(notice);

}

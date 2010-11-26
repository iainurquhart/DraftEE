<link type="text/css" href="<?=$theme_base?>css/draftee.css" rel="stylesheet" />
<script type="text/javascript" src="<?=$theme_base?>js/draftee.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$("span.draftee_publish_draft").live('click', function() {
		alert('Publish here');	
	});
});
</script>

<div id="draftee_field_container">

	<div class="draftee_is_draft">
		<h3>This is a draft of <?=$parent_id?></h3>
		<span class="draftee_publish_draft draftee_button">Publish Draft</span>
	</div>
	<div id="draftee_ajax_response"></div>
	
</div>
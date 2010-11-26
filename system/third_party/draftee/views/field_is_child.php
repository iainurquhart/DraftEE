<link type="text/css" href="<?=$theme_base?>css/draftee.css" rel="stylesheet" />
<script type="text/javascript" src="<?=$theme_base?>js/draftee.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$("span.draftee_publish_draft").live('click', function() {
		alert('Publish here');	
	});
});
</script>


<?php // echo "<pre>"; print_r($parent_data); echo "</pre>";?>
<?php // echo "<pre>"; print_r($this_data); echo "</pre>";?>



<div id="draftee_field_container">

	<div class="draftee_is_draft">
		<h3>This is a draft of <a href="<?=$publish_base?>entry_id=<?=$parent_id?>&amp;channel_id=<?=$channel_id?>"><?=$parent_data['title']?></a></h3>
		<?php if($date_mismatch){ ?>
			<p class="draftee_warning"><strong>Note:</strong> The parent has been edited since this draft was created<br />Publishing this draft will overwrite any changes made.</p>
		<?php } ?>
		
		<span class="draftee_publish_draft draftee_button">Publish Draft</span>
		
		
	</div>
	
	<div id="draftee_ajax_response"></div>
	
</div>
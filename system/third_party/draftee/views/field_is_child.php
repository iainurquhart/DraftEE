<?php // Ugly hard code to make sure EDITOR group can't publish draft ?>
<?php define('EDITOR_GROUP_ID', 7); //change the id to your corresponding Editor group id ?>


<link type="text/css" href="<?=$theme_base?>css/draftee.css" rel="stylesheet" />
<script type="text/javascript" src="<?=$theme_base?>js/draftee.js"></script>

<script type="text/javascript">
$(document).ready(function() {

	function draftee_changes_detected()
	{
		$('#draftee_publish_draft_wrapper').html('<p class="draftee_warning">You have made changes, please save your changes or refresh to cancel.</p>');
	}
	
	// This is evidently going to cause issues... but for now it'll do.
	// basically check if the author has made any changes to the data on the publish page
	// try and stop folks from publishing a draft live without them saving changes first
	
	// any fields changed?
	$(".publish_field select, .publish_field input").not('#draftee_field_container input').change( function() {
		draftee_changes_detected();
	});
	// any new content added to fields
	$('.publish_field').keyup(function() {
	  	draftee_changes_detected();
	});
	
	// do the ajax push to the module and have this entry's data overwrite the parent.
	$("span.draftee_publish_draft").live('click', function() {
	
		$('span.draftee_button').addClass('draftee_update_underway');
		
		close_drafts = $('#close_drafts:checked').val();
		if(!close_drafts) close_drafts = 0;
		
		var this_entry_id = 'entry_id=<?=$entry_id?>';
   		var this_channel_id = '&channel_id=<?=$channel_id?>';
   		var close_drafts = '&close_drafts=' + close_drafts;
   		var parent_id = '&parent_id=<?=$parent_id?>';
   		var publish_base = '<?=$publish_base?>'

		$.ajax({  
	           
            url: "<?=$ajax_base?>publish_draft",
            type: "GET",         
            data: this_entry_id + this_channel_id + close_drafts + parent_id,       
            error: function () {                
                 alert('An error occurred!');                 
            }, 
            success: function (html) {                
					
					// fetch the response message
                    var msg = html['msg'];
                    
                    // was it all good?
                    if(msg = 'entry_updated')
                    {
                    	$('span.draftee_button').removeClass('draftee_update_underway').removeClass('draftee_publish_draft').addClass('draftee_publish_done').html('Done!');
                    	$('#draftee_close_other_drafts').fadeOut();
                    }
                    // buggers...
                    else
                    {
                    	alert(msg);
                    }
            }         
        });
		
	});
	


	

});
</script>


<?php // echo "<pre>"; print_r($parent_data); echo "</pre>";?>
<?php // echo "<pre>"; print_r($this_data); echo "</pre>";?>



<div id="draftee_field_container">

	<div class="draftee_is_draft">
		<h3>This is a draft version of: <a href="<?=$publish_base?>entry_id=<?=$parent_id?>&amp;channel_id=<?=$channel_id?>"><?=$parent_data['title']?></a></h3>
		
		<div id="draftee_publish_draft_wrapper">
			<?php if($date_mismatch){ ?>
				<p class="draftee_warning"><strong>Note:</strong> The parent has been edited since this draft was created<br />Publishing this draft will overwrite any changes made.</p>
			<?php } ?>

            <?php // Ugly hard code to make sure EDITOR group can't publish draft ?>
            <?php if ($group_id != EDITOR_GROUP_ID) : ?>
			<span class="draftee_publish_draft draftee_button">Publish Draft</span> 		
			<span id="draftee_close_other_drafts"><input type="checkbox" id="close_drafts" value="1" /> Close all other drafts</span>
            <?php endif;?>
		</div>
		
	</div>
	
	<div id="draftee_ajax_response"></div>
	
</div>

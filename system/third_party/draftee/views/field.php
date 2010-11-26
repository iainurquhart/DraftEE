<link type="text/css" href="<?=$theme_base?>css/draftee.css" rel="stylesheet" />
<script type="text/javascript" src="<?=$theme_base?>js/draftee.js"></script>

<script type="text/javascript">
$(document).ready(function() {
	$("span.draftee_create_draft").live('click', function() {
   			var this_entry_id = 'entry_id=<?=$entry_id?>';
   			var this_channel_id = '&channel_id=<?=$channel_id?>';
   			
   			var publish_base = '<?=$publish_base?>'
   			$('span.draftee_button').addClass('draftee_update_underway');
   			
   			$.ajax({  
	           
	            url: "<?=$ajax_base?>",
	            type: "GET",         
	            data: this_entry_id + this_channel_id,       
	            error: function () {                
	                 alert('An error occurred!');                 
	            },
	             
	            //success  
	            success: function (html) {                
	                                   
	                    $('span.draftee_button').removeClass('draftee_update_underway');
	                    
	                    var msg = html['msg'];
	                    var draft_entry_id = html['draft_entry_id'];
	                    var draft_channel_id = html['draft_channel_id'];
	                    
	                    var edit_url = publish_base + 'channel_id=' + draft_channel_id + '&amp;entry_id=' + draft_entry_id;
	                    
	                    // alert(data);
	                    
	                    // tell the user to refresh
	                    if(msg == 'entry_created')
	                    {
	                    	$('span.draftee_button').html('<a href="' + edit_url + '">Draft created! Click to edit</a>').addClass('bulkee_extended').removeClass('draftee_create_draft');
	                    }	                    
	                   
	                   //  $.ee_notice(msg, {type: 'success'});
	                                 
	            }         
	        });

	});
});
</script>

<div id="draftee_field_container">

	<?php
		// do we have any drafts of this entry
		if(count($drafts) > 0)
		{
			$r = '<ul id="draftee_draft_list">';
			
			foreach($drafts as $draft)
			{
				$r .= '<li>draft info/options will go here</li>';
			}
			
			$r .= '</ul>';
			echo $r;
		}
		elseif(!$parent_id)
		{
			echo "<ul id='draftee_draft_list'><li>No drafts exist for this entry.</li></ul>\n";
			echo '<span class="draftee_create_draft draftee_button">Create Draft</span>';
		}
	
	?>

	
	<div id="draftee_ajax_response"></div>
	
</div>
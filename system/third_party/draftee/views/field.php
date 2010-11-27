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
	           
	            url: "<?=$ajax_base?>create_draft",
	            type: "GET",         
	            data: this_entry_id + this_channel_id,       
	            error: function () {                
	                 alert('An error occurred!');                 
	            }, 
	            success: function (html) {                
	                                   
	                    $('span.draftee_button').removeClass('draftee_update_underway');
	                    
	                    var msg = html['msg'];
	                    var draft_entry_id = html['draft_entry_id'];
	                    var draft_channel_id = html['draft_channel_id'];
	                    var edit_url = publish_base + 'channel_id=' + draft_channel_id + '&amp;entry_id=' + draft_entry_id;
	                    
	                    if(msg == 'entry_created')
	                    {
	                    	$('ul#draftee_draft_list').slideUp();
	                    	$('span.draftee_button').html('<a href="' + edit_url + '">Draft created! Click to edit</a>').addClass('bulkee_extended').removeClass('draftee_create_draft');
	                    }	                    

	            }         
	        });

	});
});
</script>

<div id="draftee_field_container">
	
	<?php
		$draft_count = 0;
		// do we have any drafts of this entry
		if(count($drafts) > 0)
		{
			
			$r = "<h3>Drafts for this entry</h3>";
			$r .= "<table id='draftee_draft_list' cellspacing='0'>";
			$r .= "<tr><th>Title</th><th>ID</th><th>Last Updated</th><th>Created By</tr>";
			foreach($drafts as $draft)
			{
				// make sure we've actually got an entry
				if(isset($draft['entry_id']) && isset($draft['screen_name']))
				{
					
					$draft_count++;
					$edit_date = $this->localize->decode_date($date_format, strtotime($draft['edit_date']), TRUE);
					$r .= "<tr>";
					
					$r .= "<td><a href='".$publish_base."&amp;entry_id=".$draft['entry_id']."&amp;channel_id=".$draft['channel_id']."'>".$draft['title']."</a></td>";
					$r .= "<td>".$draft['entry_id']."</td>";
					$r .= "<td>".$edit_date."</td>";
					$r .= "<td>".$draft['screen_name']."</td>";
					$r .= "</tr>";
				}
			}
			$r .= "</table>";
		}
		if($draft_count == 0)
		{
			echo "<ul id='draftee_draft_list'><li>No drafts exist for this entry.</li></ul>\n";
			
		}
		else
		{
			echo "<script type='text/javascript'>draftee_notify_drafts($draft_count);</script>";
			echo $r;
		}
		
		echo '<span class="draftee_create_draft draftee_button">Create a new draft</span>';
	
	?>

	
	<div id="draftee_ajax_response"></div>
	
</div>
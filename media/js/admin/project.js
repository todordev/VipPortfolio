jQuery(document).ready(function() {
	
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'project.cancel' || document.formvalidator.isValid(document.id('project-form'))) {
            Joomla.submitform(task, document.getElementById('project-form'));
        }
    };
    
	// Style file input
	jQuery("#jform_thumb").filestyle({
		buttonText: Joomla.JText._('COM_VIPPORTFOLIO_CHOOSE_FILE')
	});
	
	jQuery("#jform_image").filestyle({
		buttonText: Joomla.JText._('COM_VIPPORTFOLIO_CHOOSE_FILE')
	});
	
	var extraImagesElement = jQuery("#itp-extra-images");
	
	if(extraImagesElement) {
		
		jQuery('#fileupload').fileupload({
	        dataType: 'text json',
	        sequentialUploads: true,
	        submit: function(event, data) {
	        	
	        	var formData = {
        			thumb_width: jQuery("#extra_thumb_width").val(),
        			thumb_height: jQuery("#extra_thumb_height").val(),
        			thumb_scale: jQuery("#extra_thumb_scale").val(),
        			format: "raw",
        			id: jQuery("#jform_id").val()
        		};
        		
        		data.formData = formData;
        		
	        },
	        done: function (event, data) {
	        	var image = data.result.data;
	        	VipPortfolioHelper.addExtraImage(image);
	        }
	    });
		
		jQuery(extraImagesElement).on("mouseover", ".ai-imglink", function(event) {
			
			var url = jQuery(this).data("image-url");
			
			var img = '<img src="'+url+'" />';
			
			jQuery(this).popover({ 
				content: img, 
				html:true,
				placement: "right"
			});
			
			jQuery(this).popover("show");
		});
		
		jQuery(extraImagesElement).on("mouseout", ".ai-imglink", function(event) {
			jQuery(this).popover("hide");
		});

		jQuery(extraImagesElement).on("click", ".ai_ri", function(event) {
			
			event.preventDefault();
			
			var imageId = jQuery(this).data("image-id");
			
			jQuery.ajax({
				url: "index.php?option=com_vipportfolio&task=project.removeExtraImage",
				type: "POST",
				data: { format: "raw", id: imageId },
				dataType: "text json"
			}).done( function( response ) {
				
    	    	jQuery("#ai_box"+response.data.item_id).remove();
    	    	jQuery("#ajax_loader").hide();
    	    	
    	    	if(!response.success) {
					VipPortfolioHelper.displayMessageFailure(response.title, response.text);
				}
    	    	
			});
			
		});
	}
	
});


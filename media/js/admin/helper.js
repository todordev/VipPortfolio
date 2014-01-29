/**
 * Vip Portfolio JavaScript Helper
 */
var VipPortfolioHelper = {
		
		displayMessageSuccess: function(title, text) {
			jQuery.pnotify({
		        title: title,
		        text: text,
		        icon: "icon-ok",
		        type: "success",
	        });
		},
		displayMessageFailure: function(title, text) {
			jQuery.pnotify({
		        title: title,
		        text: text,
		        icon: 'icon-warning',
		        type: "error",
	        });
		},
		addExtraImage: function(image) {
			var imagesWrapper = jQuery("#itp-extra-images");
	    	
	    	// Image box
	    	var elTR = jQuery('<tr/>', {
			    "id": 'ai_box'+image.id
			});
	    	
	    	// TD image
	    	var elTDImage = jQuery('<td/>', {
			    "class": 'span10'
			});
	    	
	    	// Thumbnail
	    	var elImg = jQuery('<img/>', {
			    "src": image.thumb,
			    "data-image-url": image.image,
			    "class": "ai-imglink"
			});
	    	
	    	// TD actions
	    	var elTDAction = jQuery('<td/>', {
			    "class": 'span2'
			});
	    	
	    	// Icon for image removing
	    	var elBtnRemove = jQuery('<button/>', {
			    "text": Joomla.JText._('COM_VIPPORTFOLIO_REMOVE'),
			    "class": "btn btn-danger ai_ri",
			    "data-image-id": image.id
			});

	    	jQuery(elImg).appendTo(elTDImage);
	    	jQuery(elTDImage).appendTo(elTR);
	    	
	    	jQuery(elBtnRemove).appendTo(elTDAction);
	    	jQuery(elTDAction).appendTo(elTR);
	    	
	    	jQuery(elTR).appendTo(imagesWrapper);
		},
		
		loadStyleFile: function(file, myCodeMirror) {
			
			jQuery.ajax({
				url: 'index.php?option=com_vipportfolio&task=csseditor.getfile&format=raw&style_file='+file,
				method: "get",
				cache: false,
				dataType: "text",
				beforeSend: function() {
					jQuery("#ajax_loader").show();
				},
				success: function( responseText ) {
				
					// Hide loading animation
	    	    	jQuery("#ajax_loader").hide();
	    	    	
	    	    	// Check for error
	    	    	try {
	    	    		var response = jQuery.parseJSON(responseText);
	    	    		
	    	    		if(response && !response.success) {
	    					VipPortfolioHelper.displayMessageFailure(response.title, response.text);
	    					return;
	    				}
	    	    		
	    	    	} catch (e){
	    	    		
	    	    	}
	    	    	
	    	    	// Set the code to the textarea
	    	    	myCodeMirror.setValue(responseText);
	    	    	
				}
    	    	
			});
		}
}
/**
 * Vip Portfolio JavaScript Helper
 */

var VipPortfolioHelper = {
		
		displayMessageSuccess: function(title, text) {
			new Message({
				iconPath: '../media/com_vipportfolio/images/',
				icon: 'message_class_ok.png',
				title: title,
				message: text
			}).say();
		},
		displayMessageFailure: function(title, text) {
			new Message({
				iconPath: '../media/com_vipportfolio/images/',
				icon: 'message_class_warning.png',
				title: title,
				message: text
			}).say();
		},
		addExtraImage: function(image) {
			var imagesWrapper = document.id("itp-extra-images");
	    	
	    	// Image box
	    	var elDiv = new Element('div', {
			    "id": 'ai_box'+image.id,
			    "class": 'ai_box',
			});
	    	
	    	// Link
	    	var elA = new Element('a', {
			    "href": image.image,
			});

	    	// Thumbnail
	    	var elImg = new Element('img', {
			    "src": image.thumb,
			});
	    	
	    	// Icon for image removing
	    	var elImgRemove = new Element('img', {
			    "src": "../media/com_vipportfolio/images/icon_remove_16.png",
			    "class": "ai_ri",
			    "data-image-id": image.id
			});

	    	elImg.inject(elA);
	    	elA.inject(elDiv);
	    	elImgRemove.inject(elDiv);
	    	elDiv.inject(imagesWrapper);
		},
		
		loadStyleFile: function(file, myCodeMirror) {
			
			var myRequest = new Request({
	    	    url: 'index.php?option=com_vipportfolio&task=csseditor.getfile&style_file='+file,
	    	    method: "get",
	    	    format: "raw",
	    	    onRequest: function() {
	    	    	document.id("ajax_loader").show();
	    	    },
	    	    onSuccess: function(responseText){
	    	    	
	    	    	// Hide loading animation
	    	    	document.id("ajax_loader").hide();
	    	    	
	    	    	// Check for error
	    	    	try {
	    	    		var response = JSON.parse(responseText);
	    	    		
	    	    		if(!response.success) {
	    					VipPortfolioHelper.displayMessageFailure(response.title, response.text);
	    					return;
	    				}
	    	    		
	    	    	} catch (e){
	    	    		
	    	    		// Set the code to the textarea
		    	    	myCodeMirror.setValue(responseText);
		    	    	
	    	    	}
	    	    	
	    	    }
	    	});
	        
	        myRequest.send();
		}
}
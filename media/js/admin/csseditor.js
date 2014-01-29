jQuery(document).ready(function() {
	
	// Initialize the textarea
	var myTextArea   = document.id("vp_css_code");
	myTextArea.value = "";
	
	// Initialize CodeMirror 
	var myCodeMirror = CodeMirror.fromTextArea(myTextArea,{
		mode:"css",
		theme: "default"
	});
	
	// Load the content automatically
	var selectedFile = document.id('vp_style_files').value;
	if(selectedFile > 0) {
		VipPortfolioHelper.loadStyleFile(selectedFile, myCodeMirror);
	}
	
	// Set event to drop down menu that contains style files
	jQuery('#vp_style_files').on("change", function(event){
		var selectedFile = this.value;
		
		if(selectedFile == 0) {
			myCodeMirror.setValue("");
		} else {
			VipPortfolioHelper.loadStyleFile(selectedFile, myCodeMirror);
		}
        
    });
	
	// Validate script
    Joomla.submitbutton = function(task){
    	var selectedFile = document.id("vp_style_files").value;
    	
    	if(task == "csseditor.cancel" || selectedFile > 0) {
	    	myCodeMirror.toTextArea();
	        Joomla.submitform(task, document.getElementById('csseditor-form'));
    	}
        
    };
    
});

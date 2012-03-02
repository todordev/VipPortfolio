window.addEvent('domready', function(){ 
	// Validation script
    Joomla.submitbutton = function(task)
    {
        if (task == 'category.cancel' || document.formvalidator.isValid(document.id('category-form'))) {
            Joomla.submitform(task, document.getElementById('category-form'));
        }
    }
})
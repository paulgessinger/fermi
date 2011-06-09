$(document).ready(function() {
	
	
	$(window).resize(function() {
		var header_width = $('#header').outerWidth() ;
		
		$('span.sub').each(function() {
		
			$(this).css({width: (header_width - $(this).parents().position().left)}) ;
			
		}) ;
	}) ;
	
	$(window).trigger('resize') ;
	
	
	var offset = $('#actions').offset() ;
	var switched = '' ;
	
	$(window).scroll(function() {
		
		var scroll = $(window).scrollTop() ;
		
		
		
		if(scroll > offset.top)
		{
			if(switched !== true)
			{
				$('#actions').css({position: 'fixed', top: 0, right: 40}) ;
				switched = true ;
			}
		}
		else
		{
			if(switched !== false)
			{
				$('#actions').css({position: 'absolute', top: offset.top, right: 0}) ;
				
				switched = false ;
			}
		}
		
		
	}) ;
	
}) ;
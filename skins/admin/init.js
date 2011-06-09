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
	var timer = false ;
	$('.up').css({opacity: 0, display: 'none'}) ;
	$('.up').click(function() {
		
		$(window).scrollTo('#wrapper', 500) ;
		
		$('.up').stop(true).animate({opacity: 0}).queue(function() {
					
			$('.up').css({display: 'none'}) 
					
		}) ;
		
	}) ;
	
	$(window).scroll(function() {
		
		var scroll = $(window).scrollTop() ;
		
		
		
		if(scroll >= offset.top)
		{
			if(switched !== true)
			{
				switched = true ;
				
				$('#actions').css({position: 'fixed', top: 0, right: 40, marginTop: 0}) ;
				
				$('.up').css({display: 'block'}) 
				$('.up').stop(true).animate({opacity: 1}) ;
				
				
			}
		}
		
		if(scroll < offset.top)
		{
			if(switched !== false)
			{
				switched = false ;
				
				$('#actions').css({position: 'absolute', top: offset.top, right: 0, marginTop: 0}) ;

				$('.up').stop(true).animate({opacity: 0}).queue(function() {
					
					$('.up').css({display: 'none'}) 
					
				}) ;
				
				
			}
		}
		
		
	}) ;
	
}) ;
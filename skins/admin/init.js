$(document).ready(function() {
	
	
	

	
	var offset = $('#actions').offset() ;
	var switched = '' ;
	var timer = false ;
	$('.up').css({opacity: 0, display: 'none'}) ;
	$('.up').click(function() {
		
		$(window).scrollTo('#wrapper', 500, {easing: 'easeOutExpo'}) ;
		
		$('.up').stop(true).animate({opacity: 0}, 500, 'easeOutExpo').queue(function() {
					
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
				
				//alert($('#wrapper').offset().left) ;
				
				$('#actions').css({position: 'fixed', top: 0, right: ($('#wrapper').offset().left)+20}) ;
				
				
				$('.up').css({display: 'block'}) 
				$('.up').stop(true).animate({opacity: 1}, 500, 'easeOutExpo') ;
				
				
			}
		}
		
		if(scroll < offset.top)
		{
			if(switched !== false)
			{
				switched = false ;
				
				$('#actions').css({position: 'absolute', top: offset.top, right: 20}) ;

				$('.up').stop(true).animate({opacity: 0}).queue(function() {
					
					$('.up').css({display: 'none'}) 
					
				}) ;
				
				
			}
		}
		
		
	}) ;
	
	
	
	
	$(window).resize(function() {
		
		if($(window).width() >= 1680)
		{
			
			$('#wrapper').css({left: '50%', 'margin-left': -800}) ;
			
		}
		else
		{
			$('#wrapper').css({left: 40, 'margin-left': 0}) ;
		}
		
		
		
		if(switched === true)
		{
			//alert($(window).width()-$('#wrapper').outerWidth()-40) ;
			$('#actions').css({right: $('#wrapper').offset().left}) ;
		}
		
		var header_width = $('#header').outerWidth() ;
		
		$('span.sub').each(function() {
		
			$(this).css({width: (header_width - $(this).parents().position().left)}) ;
			
		}) ;
	}) ;
	
	setTimeout(function() {$(window).trigger('resize') ;}, 200) ;
	
}) ;
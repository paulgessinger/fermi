$(document).ready(function(){
 
		$('.structure_base_container ul').sortable({
			connectWith: '.structure_base_container ul',
			placeholder: 'placeholder'
		}) ;
		
		
		$($('.structure_category_label').get().reverse()).each(function() {
			
			var title = $(this).html() ;
			$(this).empty() ;
			
			var toggle = $('<span class="handle"><span class="open_icon"></span>'+title+'</span>') ;
			
			toggle.prop('open', 0) ;
			
			$(this).prepend(toggle) ;
			
			//$(this).siblings().css({height: 0, overflow: 'hidden'}) ;
			var parent = $(this).parent() ;
			var target_height = parent.outerHeight() ;
			parent.css({height: $(this).height()+10, overflow: 'hidden'}) ;
			
			//alert(parent.attr('class')) ;
			
			parent.children('.structure_category_label').siblings().css({display:'none'}) ;
			
			parent.prop('target_height', target_height) ;
			
		}) ;
		
		$('.handle').click(function() {
			
			var icon = $(this).children('.open_icon') ;
			var handle = $(this) ;
			
			if($(this).prop('open') == 1)
			{
				// close

				icon.css({backgroundPosition: 'top'}) ;
	
				
				$(this).parent().parent().prop('target_height', $(this).parent().parent().outerHeight()) ;
				
				var target_height = $(this).parent().height()+10 ;
				
				
				$(this).parent().parent().css({overflow: 'hidden'}) ;
				$(this).parent().parent().stop(true).animate({height: target_height}).queue(function() {
					
					handle.parent().siblings().css({display:'none'}) ;
					
					$(this).dequeue() ;
				}) ;
			}
			else
			{
				// open

				icon.css({backgroundPosition: 'bottom'}) ;
				
				var target_height = $(this).parent().parent().prop('target_height')-12 ;
				
				handle.parent().siblings().css({display:'block'}) ;
				
				$(this).parent().parent().stop(true).animate({height: target_height}).queue(function() {
					
					$(this).css({overflow: 'visible', height: 'auto'}) ;
					
					$(this).dequeue() ;
				}) ;
			}
			
			
			
			$(this).prop('open', (1 - $(this).prop('open'))) ;
			
		}) ;
		
		
}) ;
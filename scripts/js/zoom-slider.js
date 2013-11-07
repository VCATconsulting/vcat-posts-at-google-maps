;(function($){
			// helper
			
			// init
			$(function(){
				$('#mini-zoom-slider').slider({
					min: 1,
					max: 23,
					value: $('#mini_zoom').attr('value'),
					change:function( event, ui ){
						$('#mini_zoom').attr('value', ui.value);
					} 
				});
				$('#zoom-slider').slider({
					min: 1,
					max: 23,
					value: $('#zoom').attr('value'),
					change:function( event, ui ){
						$('#zoom').attr('value', ui.value);
					} 
				});
			});
		})(jQuery);
		
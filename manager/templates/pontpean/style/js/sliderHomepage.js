$(document).ready(function() {
	$("#slides").slides({ 
		container: "slides-container",
		generateNextPrev: true,							
		next:"slides-next-horizontal",
		prev:"slides-prev-horizontal",
		pagination: true,
		generatePagination: true,
		slideSpeed: 650,
		play: 4500,
		pause : 300,
		hoverPause: true,
		autoHeight: false,
		animationStart: function(current){
			$(".titreAlaUne").animate({bottom:-35},100);
		},
		animationComplete: function(current){
			$(".titreAlaUne").animate({bottom:30},200);
		},
		slidesLoaded: function() {
			$(".titreAlaUne").animate({bottom:30},200);
		}
	});				
		
});
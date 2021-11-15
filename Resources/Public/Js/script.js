$(document).ready(function() {

	$('#scrollwrap ul').css({'width':($('#scrollwrap ul li').length*$('#scrollwrap ul li').width()+100)+'px'}); // calculate width of timeline element //TJ
	$('#scrollwrap').horizontalScroll(); // apply horizontal scrolling to the timeline //TJ

	$('#SearchString').focus(function(){
            if ( $(this).val() == $(this).attr('value') ){
              $(this).val('');
       		}
	});

});

(function($){"use strict";$(document).ready(function(){$('#parallax-block').btparallaxfix("50%",0.5);$(window).resize(function(){$('#parallax-block').btparallaxfix("50%",0.5);});var options={slideSize:{'type':'full','size':''},parallaxType:'dynamic',item_width:200,item_height:200,rows:2,spacing:5,scroll_direction:'rtl',speed:2,next_prev_s:200,contentType:'video'};$('#parallax-block').btParallax(options);});$(document).ready(function(){$('#parallax-block-modern').btparallaxfix("50%",0.5);var options={slideSize:{'type':'full','size':'1170'},parallaxType:'dynamic',item_width:'auto',item_height:'auto',centerPadding:50,rows:1,spacing:15,scroll_direction:'rtl',speed:2,next_prev_s:200,contentType:'image'};$('#parallax-block-modern').btParallax(options);});})(jQuery);
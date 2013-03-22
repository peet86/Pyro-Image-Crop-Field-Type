var pageini = false;
$(document).ready(function() {	

    if(pageini) return;
    pageini = true;

	// JCROP 

	$('.crop_img').each(function(index) {
		var ri=$(this).data("name");
		var x=$('input[name="'+ri+'_x"]').val();
		var y=$('input[name="'+ri+'_y"]').val();
		var x2=$('input[name="'+ri+'_x2"]').val();
		var y2=$('input[name="'+ri+'_y2"]').val();
    	var r=(x2-x)/(y2-y);
       	$(this).Jcrop({
	          onSelect:  function(c){
				$('input[name="'+ri+'_x"]').val(c.x);
				$('input[name="'+ri+'_y"]').val(c.y);
	          	$('input[name="'+ri+'_x2"]').val(c.x2);
				$('input[name="'+ri+'_y2"]').val(c.y2);
	          },
			  onChange:  function(c){
				$('input[name="'+ri+'_x"]').val(c.x);
				$('input[name="'+ri+'_y"]').val(c.y);
	          	$('input[name="'+ri+'_x2"]').val(c.x2);
				$('input[name="'+ri+'_y2"]').val(c.y2);
	          },
	          bgColor:   'black',
			  boxWidth:  460,
			  boxHeight: 460,
	          bgOpacity: .3,
			  setSelect: [x,y,x2,y2],
		      aspectRatio: r
	    });
	});
 
    
     // REMOVE 
     $('.crop_image_remove').click(function(e){
        var ri=$(this).data("remove");
        $('input[name="'+ri+'"]').val('');
        $("#"+ri+"_crop_pool").remove();
        $(this).remove();
        return false;
      });
});
	

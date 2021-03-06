$(function(){
    $.fn.raty.defaults.path = "/assets/js/images/raty";
    $.fn.raty.defaults.hints = null;
    print_raty();
});


function print_raty(){
    $(".review").raty({
	readOnly: true,
	number: function(){
	    return $(this).attr('data-number');
	},
	score: function(){
	    return $(this).attr('data-score');
	},
    });

    $('.review-edit').raty({
	number: function(){
	    return $(this).attr('data-number');
	},
	score: function(){
	    return $(this).attr('data-score');
	},
	click: function(score, evt){
	    $('#review-score').val(score);
	}
    });
}

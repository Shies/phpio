$(function(){
	var class_stat = {};
	$('#function_table >tbody >tr.prof_item').each(function(){
		var cls = $(this).attr('data');
		if ( !(cls in class_stat) ) class_stat[cls] = 0;
		class_stat[cls]++;
	}).click(function(){
		if ( $(this).hasClass('show_detail') ){
			$(this).removeClass('show_detail');
			$(this).next('tr').hide();
		} else {
			$(this).addClass('show_detail');
			$(this).next('tr').show();
		}
	});
	
	for (var cls in class_stat) {
		$('#hooks').append('<li><a href="#'+cls+'" data="'+cls+'">'+cls+'<span class="label label-'+cls+'">'+class_stat[cls]+'</span></a></li>');
	}

	$('#hooks li').click(function(){
		$('.show_detail').removeClass('show_detail');
		$('#hooks li.active').removeClass('active');
		$(this).addClass('active');
		
		var cls = $("a", this).attr('data');
		if( cls != undefined ) {
			$('#function_table >tbody >tr').hide();
			$('#function_table >tbody >tr[data="'+cls+'"]').show();
		} else {
			$('#function_table >tbody >tr.prof_item').show();
		}
		return false;
	});

	$('#tabbable .tabbable .nav li>a').click(function (e) {
	    e.preventDefault();
	    $(this).tab('show');
    });

    $('.prof_detail').dblclick(function(){
    	 $(this).prev('tr').click();
    });

    $('#profiles .dropdown-toggle').click(function(){
	$.getJSON("?op=profiles",function(data){
		$('#profiles .dropdown-menu').empty().append('<li><a href="?">Auto(newest)</a></li>');
            $.each(data,function(id,uri){
                $('#profiles .dropdown-menu').append('<li><a href="?profile_id='+id+'" title="'+uri+'">'+id.substr(-4,4)+' '+uri+'</a></li>');
            })
        });
    });
})

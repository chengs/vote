jQuery(document).ready(function($) {
	//fancy box
	$('.btn-ppt').click(function(obj) {
		var id = $(obj.currentTarget).attr('name').substr(3);
		$.getJSON('index.php', {
			s : 'Index/slide',
			id : id
		}, function(data) {
			$.fancybox.open(data, {
				width:640,
				height:480,
				minWidth:640,
				minHeight:480,
				helpers : {
					title : {
						type : 'inside'
					},
					buttons : {}
				},

				afterLoad : function() {
					this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
				}
			});
		});
		return false;
	});
	//vote
	$('.btn-vote').click(function(obj) {
		var id = $(obj.currentTarget).attr('name').substr(3);
		window.location = 'index.php?a=vote&voteid='+id;
		return true;
	});
});

// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
// $(document).foundation();


// Register form
$('#registerFormBtn').click(function()
{
	$(this).find('i').toggleClass('ion-ios-arrow-up');
	$('#registerForm').toggleClass('open');
});


// areyousure
$('[id="openDialog"]').click(function(e)
{
	switch($(this).data('type')) {
		case "confirm":
			var check = confirm($(this).data('message'));
			if (!check)
			{
				e.preventDefault();
			}
		break;
		case "alert":
			alert($(this).data('message'));
		break;
	}
});


// closeSuperParent()
function closeSuperParent(el)
{
	$(el).parent().parent().fadeOut();
}


// unreadNotificationBtn
$('[id="markNotificationAsReadBtn"').click(function()
{
	var token = $('#csrftoken').val();
	var id    = $(this).data('nid');
	var el    = $(this);
	var txt   = el.text();

	$.post('/markasread', {_token:token, id:id}, function(res)
	{
		if (res == 'marked')
		{
			if (txt == 'mark as read')
			{
				el.parent().parent().parent().find('h5').removeClass('bold');
				el.text('mark as unread');
			}
			else
			{
				el.parent().parent().parent().find('h5').addClass('bold');
				el.text('mark as read');
			}
		}
	});
});
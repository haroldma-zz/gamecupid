function checkForNotification() {
	$.get('/notification', function(res)
	{
		if (res != '')
		{
			if ($('#notification').length)
			{
				$('#notification').remove();
			}

			var notification = '';

			notification += '<div class="notification" id="notification">';
			notification += '<h6>';
			notification += res.title + '<br>';
			notification += '<small>' + res.description + '</small>';
			notification += '<div class="closer" onclick="closeSuperParent(this);">';
			notification += '<i class="ion-close"></i>';
			notification += '</div>';
			notification += '</h6>';
			notification += '</div>';

			$('body').append(notification);
		}
		checkForNotification();
	});
}

setTimeout(function()
{
	checkForNotification();
}, 1000);
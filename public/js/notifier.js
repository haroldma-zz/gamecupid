function checkForNotification() {
	$.getJSON('/notification', function(res)
	{
        console.log(res);
		if (res)
		{
            if ($('#notification').length) {
                $('#notification').remove();
            }
            for (var n in res) {
                var not = res[n];
                var notification = '';

                notification += '<div class="notification" id="notification">';
                notification += '<h6>';
                notification += '<a href="/notifications">' + not.title + '</a><br>';
                notification += '<small>' + not.description + '</small>';
                notification += '<div class="closer" onclick="closeSuperParent(this);">';
                notification += '<i class="ion-close"></i>';
                notification += '</div>';
                notification += '</h6>';
                notification += '</div>';

                $('body').append(notification);
            }
		}
		setTimeout(function()
		{
			checkForNotification();
		}, 10000);
	});
}

setTimeout(function()
{
	checkForNotification();
}, 1000);
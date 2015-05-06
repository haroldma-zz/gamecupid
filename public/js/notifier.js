// Init sounds
ion.sound({
    sounds: [
        {name: "notification"}
    ],

    // main config
    path: "/sounds/",
    preload: true,
    multiplay: true,
    volume: 0.9
});

function checkForNotification() {
	$.getJSON('/notification', function(res)
	{
		if (res.length > 0)
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

                var nicon = $('#not-icon');
                var counter = $('#u-not-read-count');

                if (!nicon.hasClass('orange-text'))
                    nicon.addClass('orange-text').removeClass('ion-android-notifications-none').addClass('ion-android-notifications');

                if (counter.text() == '')
                    var count = 0;
                else
                    var count = parseInt(counter.text());

                counter.html(count + 1);

                $('body').append(notification);
                ion.sound.play("notification");

                checkForNotification();
            }
		}
        else
        {
            setTimeout(function()
            {
                checkForNotification();
            }, 10000);
        }
	});
}

setTimeout(function()
{
	checkForNotification();
}, 1000);

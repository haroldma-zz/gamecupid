function checkForNotification() {
	$.get('/notification', function(res)
	{
		console.log(res);
		// checkForNotification();
	});
}

setTimeout(function()
{
	checkForNotification();
}, 1000);
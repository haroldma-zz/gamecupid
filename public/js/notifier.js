(function checkForNotification() {
	setTimeout(function()
	{
		$.get('/notification', function(res)
		{
			console.log(res);
			// checkForNotification();
		});
	}, 1000);
})();
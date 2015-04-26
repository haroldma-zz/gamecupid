$('body').on('click', '[id="upvoter"]', function()
{
	var token = $('#csrfToken').val(),
		id    = $(this).data("invite-id");

	$.post('invite/upvote', {_token:token, id:id}, function(res)
	{
		if (res == 1)			// NORMAL UPVOTE
		{
			var points = parseInt($('#voteCount-' + id).text());
			$('#voteCount-' + id).text(points + 1);
			$('#downvoter-' + id).removeClass('activated');
			$('#upvoter-' + id).addClass('activated');
		}
		else if (res == 2)		// UNVOTED
		{
			var points = parseInt($('#voteCount-' + id).text());
			$('#voteCount-' + id).text(points - 1);
			$('#downvoter-' + id).removeClass('activated');
			$('#upvoter-' + id).removeClass('activated');
		}
		else if (res == 3)		// UPVOTED FROM DOWNVOTE
		{
			var points = parseInt($('#voteCount-' + id).text());
			$('#voteCount-' + id).text(points + 2);
			$('#downvoter-' + id).removeClass('activated');
			$('#upvoter-' + id).addClass('activated');
		}
		else if (res == 4)		// UNAUTHORIZED
		{
			window.location.href = '/login';
		}
	});
});

$('body').on('click', '[id="downvoter"]', function()
{
	var token = $('#csrfToken').val(),
		id    = $(this).data("invite-id");

	$.post('invite/downvote', {_token:token, id:id}, function(res)
	{
		if (res == 1)			// NORMAL DOWNVOTE
		{
			var points = parseInt($('#voteCount-' + id).text());
			$('#voteCount-' + id).text(points - 1);
			$('#upvoter-' + id).removeClass('activated');
			$('#downvoter-' + id).addClass('activated');
		}
		else if (res == 2)		// UNVOTED
		{
			var points = parseInt($('#voteCount-' + id).text());
			$('#voteCount-' + id).text(points + 1);
			$('#upvoter-' + id).removeClass('activated');
			$('#downvoter-' + id).removeClass('activated');
		}
		else if (res == 3)		// DOWNVOTED FROM UPVOTE
		{
			var points = parseInt($('#voteCount-' + id).text());
			$('#voteCount-' + id).text(points - 2);
			$('#upvoter-' + id).removeClass('activated');
			$('#downvoter-' + id).addClass('activated');
		}
		else if (res == 4)		// UNAUTHORIZED
		{
			window.location.href = '/login';
		}
	});
});
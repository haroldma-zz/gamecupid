$('body').on('click', '[id="upvoter"]', function()
{
	var token = $('#csrfToken').val(),
		id    = $(this).data("post-id"),
		ua    = $('#upvoter-' + id),
		da    = $('#downvoter-' + id);

	if (ua.hasClass('activated'))
		ua.removeClass('activated')
	else
		ua.addClass('activated');

	if (da.hasClass('activated'))
		da.removeClass('activated');


	$.post('/post/upvote', {_token:token, id:id}, function(res)
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
		id    = $(this).data("post-id"),
		ua    = $('#upvoter-' + id),
		da    = $('#downvoter-' + id);

	if (da.hasClass('activated'))
		da.removeClass('activated')
	else
		da.addClass('activated');

	if (ua.hasClass('activated'))
		ua.removeClass('activated');

	$.post('/post/downvote', {_token:token, id:id}, function(res)
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

$('body').on('click', '[id="comment-upvoter"]', function()
{
	var token = $('#csrfToken').val(),
		id    = $(this).data("comment-id"),
		ua    = $('#comment-upvoter-' + id),
		da    = $('#comment-downvoter-' + id);

	if (ua.hasClass('activated'))
		ua.removeClass('activated')
	else
		ua.addClass('activated');

	if (da.hasClass('activated'))
		da.removeClass('activated');

	$.post('/comment/upvote', {_token:token, id:id}, function(res)
	{
		if (res == 1)			// NORMAL UPVOTE
		{
			var points = parseInt($('#voteCountComment-' + id).text());
			$('#voteCountComment-' + id).text(points + 1);
			$('#comment-downvoter-' + id).removeClass('activated');
			$('#comment-upvoter-' + id).addClass('activated');
		}
		else if (res == 2)		// UNVOTED
		{
			var points = parseInt($('#voteCountComment-' + id).text());
			$('#voteCountComment-' + id).text(points - 1);
			$('#comment-downvoter-' + id).removeClass('activated');
			$('#comment-upvoter-' + id).removeClass('activated');
		}
		else if (res == 3)		// UPVOTED FROM DOWNVOTE
		{
			var points = parseInt($('#voteCountComment-' + id).text());
			$('#voteCountComment-' + id).text(points + 2);
			$('#comment-downvoter-' + id).removeClass('activated');
			$('#comment-upvoter-' + id).addClass('activated');
		}
		else if (res == 4)		// UNAUTHORIZED
		{
			window.location.href = '/login';
		}
	});
});

$('body').on('click', '[id="comment-downvoter"]', function()
{
	var token = $('#csrfToken').val(),
		id    = $(this).data("comment-id"),
		ua    = $('#comment-upvoter-' + id),
		da    = $('#comment-downvoter-' + id);

	if (da.hasClass('activated'))
		da.removeClass('activated')
	else
		da.addClass('activated');

	if (ua.hasClass('activated'))
		ua.removeClass('activated');

	$.post('/comment/downvote', {_token:token, id:id}, function(res)
	{
		if (res == 1)			// NORMAL DOWNVOTE
		{
			var points = parseInt($('#voteCountComment-' + id).text());
			$('#voteCountComment-' + id).text(points - 1);
			$('#comment-upvoter-' + id).removeClass('activated');
			$('#comment-downvoter-' + id).addClass('activated');
		}
		else if (res == 2)		// UNVOTED
		{
			var points = parseInt($('#voteCountComment-' + id).text());
			$('#voteCountComment-' + id).text(points + 1);
			$('#comment-upvoter-' + id).removeClass('activated');
			$('#comment-downvoter-' + id).removeClass('activated');
		}
		else if (res == 3)		// DOWNVOTED FROM UPVOTE
		{
			var points = parseInt($('#voteCountComment-' + id).text());
			$('#voteCountComment-' + id).text(points - 2);
			$('#comment-upvoter-' + id).removeClass('activated');
			$('#comment-downvoter-' + id).addClass('activated');
		}
		else if (res == 4)		// UNAUTHORIZED
		{
			window.location.href = '/login';
		}
	});
});
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
$('[id="notificationContainer"').click(function()
{
	var token  = $('#csrftoken').val();
	var id     = $(this).data('nid');
	var unrc   = parseInt($('#u-not-read-count').text());
	var icon   = $('#not-icon');

	unrc -= 1;

	$(this).find('h5').removeClass('bold');

	if (unrc > 0)
	{
		$('#u-not-read-count').text(unrc);
	}
	else
	{
		$('#not-icon').addClass('ion-android-notifications-none');
		$('#not-icon').removeClass('orange-text');
		$('#u-not-read-count').text('');
	}

	$.post('/markasread', {_token:token, id:id}, function(res)
	{
		if (res != 'marked')
		{
			if (!unrc)
			{
				unrc = 0;
			}

			unrc += 1;

			$('#u-not-read-count').text(unrc);
			$('#not-icon').removeClass('ion-android-notifications-none');
			$('#not-icon').addClass('ion-android-notifications orange-text');
		}
	});
});


// game search input
$('#gameSearchInput').on('keyup', function(e)
{
	var token = $('#csrfToken').val();
	var input = $(this).val();

	if (input == '')
	{
		e.stopPropagation();
		$('#gameSearchResults').html('');
		return false;
	}

	$('#moreDetails').addClass('disabled');
	$('#console').attr('disabled', true).addClass('disabled');
	$('#console').html('<option value="0">select a game first</option>');

	$.post('/game/search', {_token:token, title:input}, function(res)
	{
		if (res)
		{
			$('#gameSearchResults').html('');
			$.each(res, function(idx, game)
			{
				var markup = '';

				markup += '<div class="game-search-result" id="selectGame" data-id="' + game.id + '" data-title="' + game.title + '">';
				markup += '<a>' + game.title + '</a>';
				markup += '</div>';

				$('#gameSearchResults').append(markup);
			});
		}
	});
});

// game search input focus
$('#gameSearchInput').click(function(e)
{
	var gsr = $('#gameSearchResults');

	if (gsr.length && gsr[0].childElementCount > 0)
	{
		e.stopPropagation();
		$('#gameSearchResults').show();
	}
});

// game selected
$('body').on('click', '[id="selectGame"]', function()
{
	var id     = $(this).data('id'),
		title  = $(this).data('title');

	$('#gameSearchInput').val(title);
	$('#selectedGameId').val(id);

	$.get('/game/consoles/?id=' + id, function(res)
	{
		if (res.length > 0)
		{
			var list = $('#console');

			$('#moreDetails').removeClass('disabled');
			list.attr('disabled', false).removeClass('disabled');
			list.html('<option value="0">Select a console</option>');

			$.each(res, function(key, val)
			{
				list.append('<option value="' + val.id + '">' + val.name + '</option>');
			});
		}
		else
		{
			console.log('Game is not available on any of our supported consoles.');
		}
	});
});

// hide game select box if user clicks in- or outside of it
$('html, body').click(function(e)
{
	var gsr = $('#gameSearchResults');

	if (gsr.length && gsr[0].childElementCount > 0)
	{
		$('#gameSearchResults').hide();
	}
});

// category change
$('input[name="category"]').on('change', function(e)
{
	var cat = $(this).val();

	if (cat != 'planned')
		$('#plannedSection').addClass('hide');
	else
		$('#plannedSection').removeClass('hide');
});

// Submit post form
$('#postSubmitter').click(function()
{
	$(this).attr('disabled', true);
	$('#progresser').toggle();
	$('#submitError').html('');

	var	maxPlayers     = $('#maxPlayers').val(),
		gameId         = $('#selectedGameId').val(),
		consoleId      = $('#console').val(),
		title          = $('#postTitle').val(),
		text           = $('#postText').val(),
		verified       = $('#verifiedInput').prop('checked'),
		vchecked       = 'no',
		token          = $('#csrfToken').val(),
		button         = $(this),
		category       = $('input[name="category"]:checked').val(),
		startDate      = $('#startDate').val(),
		startTime      = $('#startTime').val(),
		endDate        = $('#endDate').val(),
		endTime        = $('#endTime').val();

	if (verified == true)
		vchecked = 'yes';

	$.ajax({
	    url: "/post",
	    type: "POST",
	    data: {
	    	_token: token,
	    	max_players: maxPlayers,
	    	game_id: gameId,
	    	console_id: consoleId,
	    	title: title,
	    	self_text: text,
	    	verified: vchecked,
	    	category: category,
	    	start_date: startDate,
	    	start_time: startTime,
	    	end_date: endDate,
	    	end_time: endTime
	    },
	    success: function(res)
	    {
	    	window.location.href = '/';
	    },
	    error: function(res)
	    {
			button.attr('disabled', false);
			$('#progresser').toggle();

			console.log(res);

			if (res.responseJSON != null)
			{
				$.each(res.responseJSON, function(key, error)
				{
					$('#submitError').append('<li>' + error[0] + '</li>');
				});
			}
			else
			{
				$('#submitError').append('<li>' + res.responseText + '</li>');
			}
	    }
	});
});


// reply to comment button
$('body').on('click', '[id="replyToComment"]', function()
{
	$('#commentBox-' + $(this).data('id')).toggle();
});

// platform selector
$('#feedSelector').click(function(e)
{
	e.stopPropagation();
	$('#platformList').toggleClass('open');
});

$('#platformList').click(function(e)
{
	e.stopPropagation();
});

$('body').click(function(e)
{
	if ($('#platformList').hasClass('open'))
	{
		$('#platformList').removeClass('open');
	}
});
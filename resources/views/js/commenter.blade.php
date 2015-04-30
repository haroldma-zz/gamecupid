<script>
	var emojisShowing = false;
	var foundEmojis   = [];

	$('.comment-box').on('keydown', 'textarea:first', function(e)
	{
		var keyCode = e.keyCode || e.which;

		if (keyCode === 9)
			e.preventDefault();
	});

	$('.comment-box').on('keyup', 'textarea:first', function(e)
	{
		var keyCode  = e.keyCode || e.which;
		var allInput = $(this).val();
		var input    = ':' + allInput.substring(allInput.lastIndexOf(':') + 1);

		if (emojisShowing === false && keyCode === 186 && e.shiftKey)
		{
			$('#emojis').addClass('open');
			emojisShowing = true;
		}
		else if (emojisShowing === true)
		{
			if (keyCode === 13 || keyCode == 9 || keyCode === 32)
			{
				$('#emojis').removeClass('open');
				emojisShowing  = false;

				var newInput = allInput.slice(0, -input.length);

				if (foundEmojis[0] != undefined)
					$(this).val(newInput + foundEmojis[0] + ' ');
			}
			else if (keyCode === 186 && e.shiftKey)
			{
				$('#emojis').removeClass('open');
				emojisShowing  = false;
			}
			else
			{
				$.getJSON('/emojis.json', function(emojis)
				{
					var i = 0;
					$('#emojis').html('');
					foundEmojis = [];
					$.each(emojis, function(key, val)
					{
						if (key.match("^" + input))
						{
							$('#emojis').append('<div class="emoji"><img src="' + val + '" width="20px" height="20px"/> ' + key + '</div>');

							foundEmojis.push(key);

							i++;

							if (i === 3)
								return false;
						}
					});
				});
			}
		}
	});

	$('body').on('click', '[id="commentSubmitter"]', function(e)
	{
		var parent 		 = $(this).parent();
		var textarea     = parent.find('textarea:first');
		var button       = $(this);
		var loader       = parent.find('img:first');
		var errors       = parent.find('div:last');
		var data     	 = textarea.data();
			data.comment = textarea.val();
			data.csrf    = $('#csrfToken').val();
		var commentCount = $('#inviteCommentCount');

		// Hide emoji previewer
		if (emojisShowing === true)
			$('#emojis').removeClass('open');

		// Empty errors box
		errors.html('');
		// Show loader
		loader.toggle();
		// Disable the button
		button.attr('disabled', true);
		// Post the comment
		$.post(data.url, {_token: data.csrf, parent_id: data.parenthash, self_text: data.comment}, function(res)
		{
			switch(res[0])
			{
				case '0':
					loader.toggle();
					button.attr('disabled', false);
					errors.append('<p class="alert-text">Invalid invite id.</p>');
					break;
				case '1':
					loader.toggle();
					button.attr('disabled', false);
					errors.append('<p class="alert-text">Invite not found.</p>');
					break;
				case '2':
					loader.toggle();
					button.attr('disabled', false);
					errors.append('<p class="text-alert">You forgot to write you comment.</p>');
					break;
				case '3':
					textarea.val('');
					loader.toggle();
					button.attr('disabled', false);

					if (parseInt(commentCount.text()) === 0)
					{
						$('#commentsList').html('');
					}

					var output = '';

					output += '<article class="comment ' + data.hierarchy + ' ' + (data.hierarchy == 'child' ? '' : 'no-pad-bot') + '">';
					output += '<div class="collapser" id="collapseComment">';
	                output += '<span>[–]</span>';
					output += '</div>';
					output += '<div class="collapsed-content"><small><a href="">{{ Auth::user()->username }}</a> &middot; 1 point <span class="comment-collapsed-child-count"></span></small></div>';
					output += '<header>';
					output += '<div class="voters">';
					output += '<div class="arrows">';
					output += '<div id="comment-upvoter" data-comment-id="' + res[1].hashId + '">';
					output += '<i class="ion-arrow-up-a activated" id="comment-upvoter-' + res[1].hashId + '"></i>';
					output += '</div>';
					output += '<div id="comment-downvoter" data-comment-id="' + res[1].hashId + '">';
					output += '<i class="ion-arrow-down-a" id="comment-downvoter-' + res[1].hashId + '"></i>';
					output += '</div>';
					output += '</div>';
					output += '</div>';
					output += '<div class="img"></div>';
					output += '<div class="user-meta">';
					output += '<h6>';
					output += '<a href="/">{{ Auth::user()->username }}</a>';
					output += '</h6>';
					output += '<p>';
					output += '<time data-livestamp="' + $.now() / 1000 + '"></time>';
					output += '&nbsp;';
					output += '&middot;';
					output += '&nbsp;';
					output += '<span id="voteCountComment-' + res[1].hashId + '">1</span> point';
					output += '</p>';
					output += '</div>';
					output += '</header>';
					output += '<section class="markdown-text">';
					output += res[1].self_text;
					output += '</section>';
					output += '<footer>';
					output += '<a href="' + res[1].permalink + res[1].hashId + '">permalink</a>';
	                output += '<a>&middot;</a>';
	                output += '<a id="replyToComment" data-id="' + res[1].hashId + '">reply</a>';
	                output += '<a>&middot;</a>';
	                output += '<a id="editComment" data-id="' + res[1].hashId + '"><b>edit</b></a>';
					output += '</footer>';
					output += '<div class="comment-box" id="commentBox-' + res[1].hashId + '" style="margin-top:10px;">';
					output += '<p>You can use Markdown to write comments.</p>';
					output += '<textarea class="form-control" placeholder="Write a comment" data-parenthash="' + res[1].hashId + '" data-url="' + res[1].permalink + '" data-hierarchy="' + (data.hierarchy == 'parent' ? 'child' : 'parent') + '" data-level="has-parent"></textarea>';
					output += '<button type="submit" class="btn primary medium" id="commentSubmitter">Comment</button>';
					output += '<img src="/img/loaders/dots.svg" width="40px">'
					output += '<div></div>';
					output += '</div>';
					output += '<div class="children">';
					output += '</div>';
					output += '</article>';

					if (data.level == 'no-parent')
					{
						$('#commentsList').prepend(output);
					}
					else
					{
						parent.hide();
						parent.parent().find('.children:first').prepend(output);
					}

					commentCount.text(parseInt(commentCount.text()) + 1);
					break;
				default:
					alert('Err.. Something is going wrong.');
					break;
			}
		});

	});
</script>
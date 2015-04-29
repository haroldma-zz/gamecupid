<script>
	var sortParam = window.location.href;

	$(window).scroll(function(event)
	{
		var $this     = $(this);
		var distance  = $(window).scrollTop();
		var border    = $('#feedContainer').find('article:last')[0].offsetTop;
		var threshold = border * 0.2;

		if ($this.data('loading') !== true && distance > border - threshold)
		{
			$this.data('loading', true);
			$('#feedLoader').removeClass('hide');

			loadMoreInvites();
		}
		else if ($this.data('loading') === true & distance < border - threshold)
		{
			$this.data('loading', false);
			$('#feedLoader').addClass('hide');
		}
	});

	function loadMoreInvites(sort, page)
	{
		var canLoadMore = $(window).data('canLoadMore');

		if (canLoadMore == null || canLoadMore === true)
		{
			var limit = getUrlParameter('limit') || $('#limit').val() || 10;
			var sort = getUrlParameter('sort') || $('#sortType').val() || 'hot';
			var after = $($("article.invite").slice(-1)[0]).attr("data-id");

			$('#sortType').val(sort);

			$.get('/?sort=' + sort + '&after=' + after + '&limit=' + limit, function(res)
			{
				$('#feedLoader').addClass('hide');

				switch(res.length) {
					case 0:
						$(window).data('canLoadMore', false);		// No more invites to load
						break;
					case limit:
						$(window).data('canLoadMore', true);		// Probably more invites to load
						break;
					default:
						$(window).data('canLoadMore', false);		// Returned less then 10 invites, no more invites to load
						break;
				}

				for (var i = 0; i < res.length; i++)
				{
					var output = '';
						output += '<article data-id="' + res[i].id +'" class="invite">';
						output += '<header>';
						output += '<div class="img"></div>';
						output += '<div class="user-meta">';
						output += '<h6>';
						output += '<a href="{!! url("/") !!}">' + res[i].user.username + '</a>';
						output += '</h6>';
						output += '<p>';
						output += '<time datetime="' + res[i].createdAt + '"></time>';
						output += '</p>';
						output += '</div>';
						output += '</header>';
						output += '<section>';
						output += '<h3>';
						output += '<div class="voters">';
						output += '<div class="arrows">';
						output += '<div id="upvoter" data-invite-id="' + res[i].id + '">';
						output += '<i class="ion-arrow-up-a ' + (res[i].isUpvoted == true ? "activated" : "") + '" id="upvoter-' + res[i].id + '"></i>';
						output += '</div>';
						output += '<div id="downvoter" data-invite-id="' + res[i].id + '">';
						output += '<i class="ion-arrow-down-a ' + (res[i].isDownvoted == true ? "activated" : "") + '" id="downvoter-' + res[i].id + '"></i>';
						output += '</div>';
						output += '</div>';
						output += '<div class="count" id="voteCount-' + res[i].id + '">';
						output += res[i].totalVotes;
						output += '</div>';
						output += '</div>';
						output += res[i].title;
						output += '</h3>';
						output += '</section>';
						output += '<footer>';
						output += '<a>' + res[i].maxPlayer + ' player' + (res[i].maxPlayer > 1 ? "s" : "") + '</a>';
						output += '<a>&middot;</a>';
						output += '<a href="' + res[i].permalink + '"><b>Let\'s play!</b></a>';
						output += '<a>&middot;</a>';
						output += '<a href="' + res[i].permalink + '">' + res[i].commentCount + ' comment' + (res[i].commentCount > 1 || res[i].commentCount == 0 ? "s" : "") + '</a>';
						output += '</footer>';
						output += '<hr>';
						output += '</article>';

					$('#feedContainer').append(output);
				}
				$('#pageCount').val(parseInt(page) + 1);
			});
		}
		else
		{
			$('#feedLoader').addClass('hide');
		}
	}

	function getUrlParameter(param)
	{
		var parameters = window.location.search.substring(1);
		var variables  = parameters.split('&');

	    for (var i = 0; i < variables.length; i++) 
	    {
	        var name = variables[i].split('=');
	        if (parameters[0] == param)
	        {
	            return parameters[1];
	        }
	    }
	}
</script>
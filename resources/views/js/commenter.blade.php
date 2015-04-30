<script>
	$('body').on('click', '[id="commentSubmitter"]', function(e)
	{
		var parent 		 = $(this).parent();
		var textarea     = parent.find('textarea:first');
		var button       = $(this);
		var loader       = parent.find('img:first');
		var errors       = parent.find('div:first');
		var data     	 = textarea.data();
			data.comment = textarea.val();
			data.csrf    = $('#csrfToken').val();

		// Empty errors box
		errors.html('');
		// Show loader
		loader.toggle();
		// Disable the button
		button.attr('disabled', true);
		// Post the comment
		$.post(data.url, {_token: data.csrf, parent_id: data.parenthash, self_text: data.comment}, function(res)
		{
			switch(res)
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

					var output = '';

					$('#commentsList').prepend(output);
					break;
				default:
					alert('Err.. Something is going wrong.');
					break;
			}
		});

	});
</script>
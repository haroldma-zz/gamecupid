<script>
	$('#requestInviteBtn').click(function()
	{
		$(this).attr('disabled', true);
		$('#requestLoader').show();

		$.get('{{ url('/') . "/" . Request::segment(1) . "/" . Request::segment(2) . "/" . Request::segment(3) }}/request-invite')
		.success(function(res)
		{
			$('#requestInviteBtn').text('You have requested an invite.');
			$('#requestLoader').hide();
		})
		.error(function(xhr, status, res)
		{
			$('#requestInviteBtn').attr('disabled', false);
			$('#requestLoader').hide();

			alert('Something went wrong, try again.');
		});
	});
</script>
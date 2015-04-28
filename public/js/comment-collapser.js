$('body').on('click', '[id="collapseComment"]', function()
{
	$(this).find('i').toggleClass('ion-chevron-down');
	$(this).find('i').toggleClass('ion-chevron-up');
	$(this).parent().toggleClass('collapsed');
});
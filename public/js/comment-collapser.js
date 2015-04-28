$('body').on('click', '[id="collapseComment"]', function()
{
	$(this).find('i').toggleClass('ion-chevron-down');
	$(this).find('i').toggleClass('ion-chevron-up');
	$(this).parent().toggleClass('collapsed');
});

$('.comment-collapsed-child-count').each(function() {
    var count = $(this.closest('article')).find("article").length;
    this.innerText = '(' + count + ' child' + (count === 1 ? '' : 'ren') + ')';
})
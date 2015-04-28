$('body').on('click', '[id="collapseComment"]', function()
{
    var collapsed = $(this).parent().hasClass("collapsed")
    $(this).find('span').text(collapsed ? "[–]" : "[+]")
	$(this).parent().toggleClass('collapsed');
});

$('.comment-collapsed-child-count').each(function() {
    var count = $(this.closest('article')).find("article").length;
    this.innerText = '(' + count + ' child' + (count === 1 ? '' : 'ren') + ')';
})
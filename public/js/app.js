// Foundation JavaScript
// Documentation can be found at: http://foundation.zurb.com/docs
// $(document).foundation();


// Register form
$('#registerFormBtn').click(function()
{
	$(this).find('i').toggleClass('ion-ios-arrow-up');
	$('#registerForm').toggleClass('open');
});
function validate(form, password_required) {
	var msg = '';
	
	if (trim(form.fullName.value).length == 0) {
		msg = 'Please enter full name.';
	} else if (trim(form.home_page.value).length > 0 && !isLink(trim(form.home_page.value)))
	{
		msg = 'Please enter valid link for your blog or site, or leave it empty.';
	} else if (trim(form.userName.value).length == 0) {
		msg = 'Please enter user name.';
	} else if (password_required || trim(form.pwd.value).length != 0)
	{
		if (trim(form.pwd.value).length == 0) {
			msg = 'Please enter password.';
		} else if (trim(form.again.value).length == 0) {
			msg = 'Please enter password again.';
		} else if (form.pwd.value != form.again.value) {
			msg = 'Two passwords do not match.';
		}
	}
	
	return msg;
}
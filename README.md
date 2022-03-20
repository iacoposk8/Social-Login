
# Social Login
A php library to quickly integrate a registration and login page via email, facebook and google.

This project uses codes from other repositories:

https://github.com/thephpleague/oauth2-google

https://github.com/thephpleague/oauth2-facebook

## Installation

    git clone https://github.com/iacoposk8/Social-Login
Edit the config.php file.

	$config = array(
		"google" => array(
			"id" => "XXXXXXXXXXXXXXXXXXXXXX",
			"secret" => "XXXXXXXXXXXXXXXXXXXXXX"
		),
		"facebook" => array(
			"id" => "XXXXXXXXXXXXXXXXXXXXXX",
			"secret" => "XXXXXXXXXXXXXXXXXXXXXX"
		),
		"email" => array(
			"send_mail" => array(
				"from_email" => "XXXXXXXXXXXXXXXXXXXXXX",
				"subject" => "XXXXXXXXXXXXXXXXXXXXXX"
			),
			"language" => "en",
You can remove the index called `google`, `facebook` or `email` if you don't need it.

To generate the id and secret of google and facebook you can do it from here:

https://console.developers.google.com/apis/credentials

https://developers.facebook.com/apps/

`from_email` and `subject` These are the information that will appear in the registration email if the user uses this method of accessing the site.

in `language` it is possible for now to choose `en` and `it` and in the following lines it is possible to modify the textual parts of the site and of the emails.

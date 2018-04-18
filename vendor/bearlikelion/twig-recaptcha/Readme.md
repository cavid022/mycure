# Twig Recaptcha

A simple twig extension to provide a recaptcha function to display [reCAPTCHA](https://www.google.com/recaptcha/).

**Requirements:**

* [Twig](https://github.com/fabpot/Twig)
* [ReCAPTCHA](https://github.com/AlekseyKorzun/reCaptcha-PHP-5)

## Installation
```
"require": {
	"bearlikelion/twig-recaptcha": "1.0.0",
}
```

## Example
```PHP
$twig = new Twig_Environment(new Twig_Loader_Filesystem('Views'));
$twig->addExtension(new Bearlikelion\TwigRecaptcha\Extension([
	'public' => '',
	'private' => ''
]));
```

```html
<html>
	<body>
		{{ recaptcha() }}
	</body>
</html>
```

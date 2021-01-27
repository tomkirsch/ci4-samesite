# NOTE!
Samesite is only supported in PHP > 7.3.0! If you are running an earlier version, this lib won't break anything, but samesite WON'T BET SET.

For older PHP versions, add this as the LAST item in your .htaccess rewrites:
```
<IfModule mod_rewrite.c>
	.
	.
	.
	# SameSite cookie - ensure this comes AFTER your rewrites!
	Header always edit Set-Cookie (.*) "$1; SameSite=Lax"
</IfModule>
```
To control the samesite value, you'd need to add logic to your .htaccess file.

# Installation

Set the response class in `App\Config\Services`:
```
	public static function response(App $config = null, bool $getShared = true){
		if ($getShared) return static::getSharedInstance('response', $config);
		if (!is_object($config)) $config = config(App::class);
		return new \Tomkirsch\Samesite\SamesiteResponse($config);
	}
```

Now you can specify a different setting with $response->setCookie():
```
$this->response->setCookie('foo', 'bar', 60 * 60 * 24, '/', '', FALSE, FALSE, 'Strict');
or
$this->response->setCookie([
	'name'=>'foo',
	'value'=>'bar',
	'samesite'=>'Strict',
]);
```

(optional) If you'd like the default samesite to be something other than Lax, you can add this to the cookie section of `App\Config\App`:
```
public $cookieSameSite = 'Lax';
````

One more thing... don't use setcookie() directly!
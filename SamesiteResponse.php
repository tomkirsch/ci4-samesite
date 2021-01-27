<?php namespace Tomkirsch\Samesite;

use CodeIgniter\HTTP\Response;

class SamesiteResponse extends Response{
	
	// determine if we can set samesite cookie
	static function canDoSameSite():bool{
		return version_compare(phpversion(), '7.3.0', '>=');
	}
	
	protected $cookieSameSite = 'Lax';
	
	// override setCookie() to allow passing of the samesite parameter
	public function setCookie(
		$name,
		$value = '',
		$expire = '',
		$domain = '',
		$path = '/',
		$prefix = '',
		$secure = false,
		$httponly = false,
		$samesite = 'Lax'
	)
	{
		if (is_array($name))
		{
			// always leave 'name' in last place, as the loop will break otherwise, due to $$item
			foreach (['value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'samesite', 'name'] as $item)
			{
				if (isset($name[$item]))
				{
					$$item = $name[$item];
				}
			}
		}

		if ($prefix === '' && $this->cookiePrefix !== '')
		{
			$prefix = $this->cookiePrefix;
		}

		if ($domain === '' && $this->cookieDomain !== '')
		{
			$domain = $this->cookieDomain;
		}

		if ($path === '/' && $this->cookiePath !== '/')
		{
			$path = $this->cookiePath;
		}

		if ($secure === false && $this->cookieSecure === true)
		{
			$secure = $this->cookieSecure;
		}

		if ($httponly === false && $this->cookieHTTPOnly !== false)
		{
			$httponly = $this->cookieHTTPOnly;
		}
		
		if ($samesite === 'Lax' && $this->cookieSameSite !== 'Lax')
		{
			$samesite = $this->cookieSameSite;
		}

		if (! is_numeric($expire))
		{
			$expire = time() - 86500;
		}
		else
		{
			$expire = ($expire > 0) ? time() + $expire : 0;
		}

		$this->cookies[] = [
			'name'     => $prefix . $name,
			'value'    => $value,
			'expires'  => $expire,
			'path'     => $path,
			'domain'   => $domain,
			'secure'   => $secure,
			'httponly' => $httponly,
			'samesite'	=> $samesite,
		];

		return $this;
	}
	
	// if PHP can't do samesite, setcookie() throw an error. This ensures we don't send it.
	protected function sendCookies()
	{
		if ($this->pretend)
		{
			return;
		}

		foreach ($this->cookies as $params)
		{
			// PHP cannot unpack array with string keys
			$params = array_values($params);
			
			// if setcookie() does not support samesite, then leave it out (set this in .htaccess instead)
			if(!static::canDoSameSite()){
				array_pop($params);
			}
			setcookie(...$params);
		}
	}
	
	
} 
<?php

class Pusher
{


    /**
     * Image
     * @const str
     */
    public const IMG = 'img';

    /**
     * Scr
     * @const str
     */
    public const SRC = 'src';

    /**
     * Link
     * @const str
     */
    public const LINK = 'link';

	/**
	 * instance
	 * @var Pusher
	 */
	private static $instance;

	/**
	 * pusher links
	 * @var array
	 */
	protected $links = [];

	/**
	 * as types
	 * @var array
	 */
	protected $as = 
	[
		Pusher::SRC => 'scripts',
		Pusher::IMG => 'image',
		Pusher::LINK => 'style'
	];


	public static function getInstance()
	{
		if (static::$instance === null) {
			static::$instance = new static;
		}

		return static::$instance;
	}

    /**
     * @param string $link
     * @param array $opts
     * @return $this
     */
	public function link(string $link, array $opts = [])
	{
		return $this->set(static::LINK, $link, $opts);
	}

    /**
     * @param string $src
     * @param array $opts
     * @return $this
     */
	public function src(string $src, array $opts = [])
	{
		return $this->set(static::SRC, $src, $opts);
	}

    /**
     * @param string $img
     * @param array $opts
     * @return $this
     */
	public function img(string $img, array $opts = [])
	{
		return $this->set(static::IMG, $img, $opts);
	}

    /**
     * @param string $type
     * @param string $link
     * @param array $opts
     * @return $this
     */
	public function set(string $type, string $link, array $opts = [])
	{
		$this->links[$type][$link] = $opts;
		return $this;
	}

    /**
     * @param string|null $type
     * @return string
     * @throws Exception
     */
	public function getHeader(string $type = null): string
	{
		$line = [];
	
		if ($type === null && (bool) $this->links) {

			foreach ($this->links as $type => $urls)
			{
				$line[] = $this->toHeader($type, $urls);
			}
			
		} elseif (isset($this->links[$type])) {

			$line[] = $this->toHeader($type, $this->links[$type]);
		
		} else {

			throw new Exception("header type is not valid");
		}

		return implode($line, ', ');
	}

    /**
     * @param string|null $type
     * @throws Exception
     */
	public function push(string $type = null): void
	{
		if (headers_sent($f, $l)) {
			throw new Exception("headers already sent at file: {$f}, line: {$l}");
		}

		header("Link: " . $this->getHeader());
	}

	/**
	 * urls to header string
	 * @param  string $type
	 * @param  array  $urls
	 * @return string|null      
	 */
	public function toHeader(string $type, array $urls): ?string
	{
		if ((bool) $urls === false) return null;

		$line = [];
		$opts = [
			'rel' => 'preload',
			'as'  => $this->as[$type] ?? false
		];

		foreach ($urls as $url => $ops)
		{
			$ops = array_merge($opts, $ops);
			$ops = $this->arrayOptionsToStr($ops);
			$line[] = "<{$url}>; {$ops}";
		}

		return implode(', ', $line);
	}

	/**
	 * convert options to string
	 * @param  array  $options
	 * @return string       
	 */
	protected function arrayOptionsToStr(array $options): string
	{
		$opts = [];

		foreach ($options as $k => $v)
		{
			if ($v === false) continue;

			$opts[] = "{$k}={$v}";
		}

		return implode('; ', $opts);
	}

	protected function __construct() { }
	protected function __clone() { }
	protected function __wakeup() { }
}

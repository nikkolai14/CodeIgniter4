<?php namespace CodeIgniter\Cache;

class CacheFactoryTest extends \CIUnitTestCase
{
	private static $directory = 'CacheFactory';
	private $cacheFactory;
	private $config;

	public function setUp()
	{
		parent::setUp();

		$this->cacheFactory = new CacheFactory();

		//Initialize path
		$this->config             = new \Config\Cache();
		$this->config->storePath .= self::$directory;
	}

	public function tearDown()
	{
		if (is_dir($this->config->storePath))
		{
			chmod($this->config->storePath, 0777);
			rmdir($this->config->storePath);
		}
	}

	public function testNew()
	{
		$this->assertInstanceOf(CacheFactory::class, $this->cacheFactory);
	}

	/**
	 * @expectedException        \CodeIgniter\Cache\Exceptions\CacheException
	 * @expectedExceptionMessage Cache config must have an array of $validHandlers.
	 */
	public function testGetHandlerExceptionCacheInvalidHandlers()
	{
		$this->config->validHandlers = null;

		$this->cacheFactory->getHandler($this->config);
	}

	/**
	 * @expectedException        \CodeIgniter\Cache\Exceptions\CacheException
	 * @expectedExceptionMessage Cache config must have a handler and backupHandler set.
	 */
	public function testGetHandlerExceptionCacheNoBackup()
	{
		$this->config->backupHandler = null;

		$this->cacheFactory->getHandler($this->config);
	}

	/**
	 * @expectedException        \CodeIgniter\Cache\Exceptions\CacheException
	 * @expectedExceptionMessage Cache config must have a handler and backupHandler set.
	 */
	public function testGetHandlerExceptionCacheNoHandler()
	{
		$this->config->handler = null;

		$this->cacheFactory->getHandler($this->config);
	}

	/**
	 * @expectedException        \CodeIgniter\Cache\Exceptions\CacheException
	 * @expectedExceptionMessage Cache config has an invalid handler or backup handler specified.
	 */
	public function testGetHandlerExceptionCacheHandlerNotFound()
	{
		unset($this->config->validHandlers[$this->config->handler]);

		$this->cacheFactory->getHandler($this->config);
	}

	public function testGetDummyHandler()
	{
		if (! is_dir($this->config->storePath))
		{
			mkdir($this->config->storePath, 0555, true);
		}

		$this->config->handler = 'dummy';

		$this->assertInstanceOf(\CodeIgniter\Cache\Handlers\DummyHandler::class, $this->cacheFactory->getHandler($this->config));

		//Initialize path
		$this->config             = new \Config\Cache();
		$this->config->storePath .= self::$directory;
	}
}

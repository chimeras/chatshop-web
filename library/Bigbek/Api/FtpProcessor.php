<?php

namespace Bigbek\Api;

class FtpProcessor
{

	private $_connection = null;
	private $_config = null;

	public function __construct()
	{
		$gConfig = \Zend_Registry::get('config');
		$this->_config = $gConfig->datafeed;
		$this->_connect($this->_config);
	}

	public function __destruct()
	{
		ftp_close($this->_connection);
	}

	public function processDownload($delete = true)
	{
		$downloaded = array();
		$localUri = $this->_config->local_path;
		$remotePath = $this->_config->path;
		foreach ($this->_getFiles($remotePath) as $filename) {
			$copy = ftp_get($this->_connection, $localUri .$filename, $remotePath.$filename, FTP_BINARY);
			if($copy && $delete){
				ftp_delete($this->_connection, $remotePath.$filename);
			}
			$downloaded[] = $filename;
		}
		return $downloaded;
	}

	protected function _connect($config)
	{
		if ($this->_connection === null) {
			if ($config->ssl) {
				$connection = ftp_ssl_connect($config->host, $config->port, $config->timeout);
			} else {
				$connection = ftp_connect($config->host, $config->port, $config->timeout);
			}
			if ($connection === false) {
				throw new \Zend_Exception('Unable to connect to host "' . $config->host . '" on port ' . $config->port);
			}

			$this->_connection = $connection;

			$login = ftp_login($this->_connection, $config->user, $config->pass);
			if ($login === false) {
				throw new \Zend_Exception('Unable to login with username "' . $config->user);
			}

			$path = ftp_pwd($this->_connection);
			if ($path === false) {
				throw new \Zend_Exception('Unable to get current directory');
			}

			$this->_currentPath = $path;
		}
	}

	public function changeDirectory($path)
	{
		ftp_chdir($this->_connection, $path);
	}

	private function _getFiles($path)
	{
		return ftp_nlist($this->_connection, $path);
	}

}
<?php

namespace ElImEx;

use Illuminate\Database\Capsule\Manager as Capsule;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Loader {
	public static $pluginBaseDir;
	public static $pluginBaseUrl;
	protected $version;

	public function __construct($pluginBaseDir, $pluginBaseUrl) {
		static::$pluginBaseDir = $pluginBaseDir;
		static::$pluginBaseUrl = $pluginBaseUrl;

		$this->version = '1.0.0';
	}

	public function init() {
		$this->setupEloquent();
		$this->initCommands();
	}

	protected function setupEloquent() {
		$params = [
			'driver' => 'mysql',
			'strict' => false,
			'database' => DB_NAME,
			'username' => DB_USER,
			'password' => DB_PASSWORD,
			'host' => DB_HOST,
			'prefix' => null,
			'charset' => 'utf8',
			'collation' => 'utf8_unicode_ci',
		];

		$dbManager = new Capsule();
		$params['prefix'] = 'wp_';
		$dbManager->addConnection($params, 'default');

		//explictly setting this option to emulate lack of mysqlnd on WP Engine.
		$dbManager
			->getConnection()
			->getPdo()
			->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, true);
		$dbManager->setAsGlobal();
		$dbManager->bootEloquent();
	}

	public function initCommands() {
		\ElImEx\Commands\ElementorExportCommand::register();
	}
}

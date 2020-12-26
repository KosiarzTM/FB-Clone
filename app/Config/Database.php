<?php namespace Config;

/**
 * Database Configuration
 *
 * @package Config
 */

class Database extends \CodeIgniter\Database\Config
{
	/**
	 * The directory that holds the Migrations
	 * and Seeds directories.
	 *
	 * @var string
	 */
	public $filesPath = APPPATH . 'Database/';

	/**
	 * Lets you choose which connection group to
	 * use if no other is specified.
	 *
	 * @var string
	 */
	public $defaultGroup = 'default';

	/**
	 * The default database connection.
	 *
	 * @var array
	 */
	public $default = [
		'DSN'      => 'postgre://zconxwegclxsuz:3de450c2f375d86fdfbf9e86d6a035425d8c3a3818b4a14d4390cd0809fd82d8@ec2-54-247-107-109.eu-west-1.compute.amazonaws.com:5432/d5etglp1inf7eo',
		'hostname' => 'ec2-54-247-107-109.eu-west-1.compute.amazonaws.com',
		'username' => 'zconxwegclxsuz',
		'password' => '3de450c2f375d86fdfbf9e86d6a035425d8c3a3818b4a14d4390cd0809fd82d8',
		'database' => 'd5etglp1inf7eo',
		'DBDriver' => 'postgre',
		'DBPrefix' => '',
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'cacheOn'  => false,
		'cacheDir' => '',
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 5432,
	];
	// public $default = [
	// 	'DSN'      => '',
	// 	'hostname' => 'localhost',
	// 	'username' => '',
	// 	'password' => '',
	// 	'database' => '',
	// 	'DBDriver' => 'MySQLi',
	// 	'DBPrefix' => '',
	// 	'pConnect' => false,
	// 	'DBDebug'  => (ENVIRONMENT !== 'production'),
	// 	'cacheOn'  => false,
	// 	'cacheDir' => '',
	// 	'charset'  => 'utf8',
	// 	'DBCollat' => 'utf8_general_ci',
	// 	'swapPre'  => '',
	// 	'encrypt'  => false,
	// 	'compress' => false,
	// 	'strictOn' => false,
	// 	'failover' => [],
	// 	'port'     => 3306,
	// ];

	/**
	 * This database connection is used when
	 * running PHPUnit database tests.
	 *
	 * @var array
	 */
	public $tests = [
		'DSN'      => '',
		'hostname' => '127.0.0.1',
		'username' => '',
		'password' => '',
		'database' => ':memory:',
		'DBDriver' => 'SQLite3',
		'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'cacheOn'  => false,
		'cacheDir' => '',
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// Ensure that we always set the database group to 'tests' if
		// we are currently running an automated test suite, so that
		// we don't overwrite live data on accident.
		if (ENVIRONMENT === 'testing')
		{
			$this->defaultGroup = 'tests';

			// Under Travis-CI, we can set an ENV var named 'DB_GROUP'
			// so that we can test against multiple databases.
			if ($group = getenv('DB'))
			{
				if (is_file(TESTPATH . 'travis/Database.php'))
				{
					require TESTPATH . 'travis/Database.php';

					if (! empty($dbconfig) && array_key_exists($group, $dbconfig))
					{
						$this->tests = $dbconfig[$group];
					}
				}
			}
		}
	}

	//--------------------------------------------------------------------

}

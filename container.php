<?php

use abenevaut\BedrockConsole\WpCli;
use Doctrine\DBAL\DriverManager as DatabaseDriverManager;

foreach (array(__DIR__ . '/../../../bedrock/config/application.php', __DIR__ . '/../../config/application.php', __DIR__ . '/../config/application.php', __DIR__ . '/config/application.php') as $file) {
    if (file_exists($file)) {
        @require_once($file);
        break;
    }
}

class Container
{

    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $databaseDriver;

    /**
     * @var WpCli
     */
    private $wpCli;

    /**
     * Container constructor.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __construct()
    {
        $this->databaseDriver = DatabaseDriverManager::getConnection([
            'driver' => 'mysqli',
            'dbname' => DB_NAME,
            'user' => DB_USER,
            'password' => DB_PASSWORD,
            'host' => DB_HOST,
            'charset' => DB_CHARSET
        ]);
        $this->wpCli = new WpCli();

        // Avoid "Unknown database type enum requested" error
        // http://fabien.agranier.com/fr/symfonydoctrine-resoudre-unknown-database-type-enum-requested/
        $this
            ->databaseDriver
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');
    }

    public function toArray()
    {
        return [
            'db' => $this->databaseDriver,
            'wp' => $this->wpCli,
        ];
    }
}

return (new Container())->toArray();

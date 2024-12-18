<?php

namespace Commands;

use Database\Connection\Connection;
use Database\Connection\DatabaseException;
use PDO;

class MigrateCommand implements ConsoleCommand {
    function getName(): string {
        return 'migrate';
    }

    function getDescription(): string {
        return 'Run database migrations.';
    }

    /**
     * @throws DatabaseException
     */
    private function dropAllTables(Connection $connection): void {
        echo "Dropping all tables..." . PHP_EOL;
        $dropAllTables = <<<SQL
          SET FOREIGN_KEY_CHECKS = 0; 
          SET @tables = NULL;
          SET GROUP_CONCAT_MAX_LEN=32768;
          SELECT GROUP_CONCAT('`', table_schema, '`.`', table_name, '`') INTO @tables
          FROM   information_schema.tables 
          WHERE  table_schema = (SELECT DATABASE());
          SELECT IFNULL(@tables, '') INTO @tables;
          SET        @tables = CONCAT('DROP TABLE IF EXISTS ', @tables);
          PREPARE    stmt FROM @tables;
          EXECUTE    stmt;
          DEALLOCATE PREPARE stmt;
          SET        FOREIGN_KEY_CHECKS = 1;
        SQL;

        $connection->getPDO()->beginTransaction();

        if ($connection->getPDO()->exec($dropAllTables) === false) {
            $errorCode = $connection->getPDO()->errorCode();
            if ($connection->getPDO()->inTransaction()) $connection->getPDO()->rollBack();
            $connection->disconnect();
            throw new \Exception("Could not drop all tables: {$errorCode}");
        }

        if ($connection->getPDO()->inTransaction()) $connection->getPDO()->commit();
    }

    /**
     * @throws \Exception
     */
    private function createMigrationsTable(Connection $connection): void {
        $createMigrationsTable = <<<SQL
          CREATE TABLE IF NOT EXISTS migrations (
              version INTEGER NOT NULL PRIMARY KEY,
              name VARCHAR(255),
              migrated_at TIMESTAMP DEFAULT NOW()
          );
        SQL;

        $connection->getPDO()->beginTransaction();

        if ($connection->getPDO()->exec($createMigrationsTable) === false) {
            $errorCode = $connection->getPDO()->errorCode();
            if ($connection->getPDO()->inTransaction()) $connection->getPDO()->rollBack();
            $connection->disconnect();
            throw new \Exception("Could not create migrations table: {$errorCode}");
        }

        if ($connection->getPDO()->inTransaction()) $connection->getPDO()->commit();
    }

    /**
     * @throws \Exception
     */
    private function migrate(PDO $pdo, int $version, string $name): void {
        echo "Migrating '" . $name . "'..." . PHP_EOL;
        $content = file_get_contents('database/migrations/' . $name);
        if ($content === false)
            throw new \Exception("Could not read migration file: '" . $name . "'");
        if ($pdo->exec($content) === false) {
            throw new \Exception($pdo->errorInfo()[2]);
        }

        $stmt = $pdo->prepare("INSERT INTO migrations (version, name) VALUES (:version, :name);");
        if(!$stmt->execute([':version' => $version, ':name' => $name]))
            throw new \Exception("Could not insert record into migrations table: '" . $name . "'");
    }

    function run(array $args): void {
        $connection = Connection::getInstance();
        $pdo = $connection->connect();

        if (count($args) > 0 && strtolower($args[0]) === 'fresh') {
            // Drop all tables.
            $this->dropAllTables($connection);
        }

        $migrations = array();

        $error = false;
        $files = scandir('database/migrations', SCANDIR_SORT_ASCENDING);
        foreach ($files as $file) {
            if (is_dir($file)) continue;
            if (!str_starts_with($file, 'V')) {
                echo "Invalid migration filename: '" . $file . "'. It should be 'V<version>__<name>.sql'" . PHP_EOL;
                $error = true;
                continue;
            }

            $versionBegin = 1;
            $versionEnd = strpos($file, '__');

            if ($versionEnd === false) {
                echo "Invalid migration filename: '" . $file . "'. It should be 'V<version>__<name>.sql'" . PHP_EOL;
                $error = true;
                continue;
            }

            $versionStr = substr($file, $versionBegin, $versionEnd - $versionBegin);

            if ($versionStr === '' || is_int($versionStr)) {
                echo "Invalid migration filename: '" . $file . "'. It should be 'V<version>__<name>.sql'" . PHP_EOL;
                $error = true;
                continue;
            }

            $version = intval($versionStr);

            if (array_key_exists($version, $migrations)) {
                echo "There already is a migration ('" . $migrations[$version] . "') with the same version as: '" . $file . "'" . PHP_EOL;
                $error = true;
                continue;
            }

            $migrations[$version] = $file;
        }

        if ($error) {
            $connection->disconnect();
            throw new \Exception("There are errors in migration file names. Aborting.");
        }

        if (count($migrations) === 0) {
            $connection->disconnect();
            echo "No migrations were found." . PHP_EOL;
            return;
        }

        ksort($migrations);

        $this->createMigrationsTable($connection);

        $latestVersionQuery = $pdo->query("SELECT MAX(version) FROM migrations");
        $latestVersionQuery->execute();
        /** @var int|false|null $latestVersion */
        $latestVersion = $latestVersionQuery->fetchColumn();
        if ($latestVersion === false || $latestVersion === null) {
            $latestVersion = null;
            echo "Currently there are no applied migrations." . PHP_EOL;
        } else {
            echo "Currently applied migrations are at version: " . $latestVersion . "." . PHP_EOL;
        }

        $pdo->beginTransaction();

        $atLeastOneMigrationHasBeenApplied = false;
        foreach ($migrations as $version => $migration) {
            if ($latestVersion !== null && $version <= $latestVersion) continue;
            $atLeastOneMigrationHasBeenApplied = true;
            try {
                $this->migrate($pdo, $version, $migration);
            } catch (\Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                $connection->disconnect();
                throw new \Exception("Could not migrate '" . $migration . "': " . $e->getMessage());
            }
        }

        if ($pdo->inTransaction()) $pdo->commit();

        if ($atLeastOneMigrationHasBeenApplied) {
            $newLatestVersionQuery = $pdo->query("SELECT MAX(version) FROM migrations");
            $newLatestVersionQuery->execute();
            /** @var int|false|null $newLatestVersion */
            $newLatestVersion = $newLatestVersionQuery->fetchColumn();

            if ($latestVersion === null) {
                echo "Successfully migrated to version " . $newLatestVersion . "." . PHP_EOL;
            } else {
                echo "Successfully migrated from version " . $latestVersion . " to version " . $newLatestVersion . "." . PHP_EOL;
            }
        } else {
            echo "No new migrations were applied." . PHP_EOL;
        }

        $connection->disconnect();
    }
}
<?php

class migration_1653895834 extends Migration {

	public function up() {
        DB::query('
            CREATE TABLE `marketplace` (
              `id` int unsigned NOT NULL AUTO_INCREMENT,
              `uid` mediumint unsigned NOT NULL DEFAULT 0 COMMENT \'User id\',
              `did` mediumint unsigned NOT NULL DEFAULT 0 COMMENT \'Village id\',
              `rid1` tinyint unsigned NOT NULL DEFAULT 0,
              `rid2` tinyint unsigned NOT NULL DEFAULT 0,
              `m1` mediumint unsigned NOT NULL DEFAULT 0,
              `m2` mediumint unsigned NOT NULL DEFAULT 0,
              `ratio` float unsigned NOT NULL DEFAULT 0,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
	}

	public function down() {
        DB::query('DROP TABLE marketplace');
	}
}

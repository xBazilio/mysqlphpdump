#!/usr/bin/php
<?php
/**
 * Mysqlphpdump
 *
 * @author Bazilio <x.bazilio@gmail.com>
 * @license http://bazilio.mit-license.org/
 */

$config = array(
    // mysql server settings
    'db' => array(
        'host' => 'localhost',
        'user' => 'root',
        'pass' => 'mysqlpass'
    ),
    // where to store backup
    'path' => '/home/bazilio/temp/mysql_dumps/db_backup'
);

//no execution time limit
ini_set('max_execution_time', 0);

//connect to server
print "Connecting to mysql server...\n";
/* @var $db mysqli */
$db = mysqli_connect($config['db']['host'], $config['db']['user'], $config['db']['pass']);

//get DB list
print "Getting DBs list...\n";
$allDtabases = array();
/* @var $res mysqli_result */
$res = $db->query('SHOW DATABASES');
$skipDbs = array('phpmyadmin', 'performance_schema', 'mysql', 'information_schema');
while ($row = $res->fetch_row()) {
    //skip system databases
    if (in_array($row[0], $skipDbs)) {
        continue;
    }
    $allDtabases[] = $row[0];
}

print "Dumping DBs...\n";
$commandTpl = 'mysqldump -h%s -u%s -p%s --add-drop-table --disable-keys --single-transaction %4$s > %5$s/%4$s.sql';
foreach ($allDtabases as $dbname) {
    $command = sprintf($commandTpl, $config['db']['host'], $config['db']['user'], $config['db']['pass'], $dbname, $config['path']);
    shell_exec($command);
    print '.';
}
print "\n";

print "Done!\n";

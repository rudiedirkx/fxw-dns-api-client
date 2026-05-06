<?php

use rdx\fxwdns;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/env.php';

$client = new fxwdns\Client(new fxwdns\WebAuth(FXW_USER, FXW_PASS));
dump($client->logIn());

echo "\n";

dump($client->customer);

echo "\n";

$domains = $client->getDomains();
dump($domains);

$domain = array_reduce($domains, function($result, fxwdns\Domain $domain) {
	return $domain->name == TEST_DOMAIN ? $domain : $result;
}, null);
dump($domain);

if ( !$domain ) return;

$txtRecords = $client->findDnsRecords($domain, ['type' => 'TXT']);

$delete = array_filter($txtRecords, function(fxwdns\DnsRecord $record) {
	return preg_match('#(^|\.)dns\d+\.#', $record->name) > 0;
});
$delete = $delete ? $delete[ array_rand($delete) ] : null;
dump($delete);

if ( $delete ) {
	dump($client->deleteDnsRecord($domain, $delete));
}

$add = new fxwdns\DnsRecord(0, 'dns' . rand(101, 999) . '.' . $domain->name, 'txt', rand(), 300);
dump($add);
dump($client->addDnsRecord($domain, $add));

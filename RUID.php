<?php

//////////////
// Create a random ID that increments over time and still supports uniqueness
//////////////

define('START', microtime(TRUE));

register_shutdown_function(function()
{
	print (microtime(TRUE) - START) . " seconds\n";
});

function randomBytes($count)
{
	// Try OpenSSL's random generator
	if (function_exists('openssl_random_pseudo_bytes')) {
		$strongCrypto = false;
		$output = openssl_random_pseudo_bytes($count, $strongCrypto);
		if ($strongCrypto && strlen($output) == $count) {
			return $output;
		}
	}

	// Try reading from /dev/urandom, if present
	$output = '';
	if (is_readable('/dev/urandom') && ($fh = fopen('/dev/urandom', 'rb'))) {
		$output = fread($fh, $count);
		fclose($fh);
		return $output;
	}
}

// Max PHP int size (32bit = 2147483647, 64bit = 9223372036854775807)
if(PHP_INT_MAX === 2147483647) {
	die("Please upgrade to 64bit computing\n");
}

// Convert to decimals so we can add them
$time = str_replace('.', '', microtime(TRUE));

// one byte can make a max value of 255
// two bytes can make a max value of 255*255 = 65535
$bytes = hexdec(bin2hex(randomBytes(2)));

// Pad the string to five digits for a uniform response
// Not a good idea because it removes many possible results
// since 44 equals 44000 as does 44000
//$bytes = str_pad($bytes, 5);

print 'int ' . $time . ' ' . $bytes . "\n";

$int = (int) ($time . $bytes);
var_dump($int);

print "Cast to base32 for use in URL's or something:\n";
print "\t" . ($l = base_convert($int, 10, 32)) . ' = ' . strlen($l) . "\n";

if(function_exists('gmp_strval')) {
	print "Cast to base62 for use in URL's or something:\n";
	print "\t" . ($l = gmp_strval(gmp_init($int, 10), 62)) . ' = ' . strlen($l) . "\n";
}

print "\nSimulate a collision:\n\n";

$average = array();
for ($i=0; $i < 100; $i++) { 

	$time = str_replace('.', '', microtime(TRUE));
	$hashes = array();
	while(true) {

		$bytes = str_pad(hexdec(bin2hex(randomBytes(2))), 5);
		$int = (int) ($time . $bytes);

		if(isset($hashes[$int])) {
			break;
		}

		$hashes[$int] = 1;
	}

	$average[] = count($hashes);
}

print (array_sum($average) / count($average)) . " average hashes before collision\n";

/*
At average of 100 hashes before collision,
you can create 100,000 inserts a second without a problem
*/
#!/usr/bin/php
<?php
// this script needs the above hashbang
// this script needs to be executable by anyone

// we'll keep a log of incoming mail here
// make sure it's writable by php
$mail_log_file = '/opt/lol/mailtest.log';

// we'll use this awesome helper class
// found here: https://code.google.com/p/php-mime-mail-parser/
require_once('MimeMailParser.class.php');

// read the incoming email from stdin
$fd = fopen("php://stdin", "r");

// initialize the mail parser
$parser = new MimeMailParser();

// send stdin to parser
$parser->setStream($fd);

// these will hold the mail info
$from = '';
$subject = '';
$date = '';
$message = '';

// parse email fields
$from = $parser->getHeader('from');
$subject = $parser->getHeader('subject');
$date = $parser->getHeader('date');

// parse message text
$message_text = $parser->getMessageBody('text');
$message_html = $parser->getMessageBody('html'); // may be null

// we'll save this info in a log file
$email_raw = $parser->getHeadersRaw() . $message_text;
$email_html = $parser->getHeadersRaw() . $message_html;

$logfile = fopen($mail_log_file, 'a');
fwrite($logfile, $email_raw."\n\n");
fclose($logfile);

/*

    from here you can do whatever you want with the mail data!

*/
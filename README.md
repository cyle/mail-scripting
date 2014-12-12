# Processing Emails with Scripts

## Setup

Sometimes I need to set up a server to process incoming email with a script. Here's how I do it.

Running Ubuntu 14.04 to start with. Make sure your server has a fully qualified domain name (whatever.com) and whatnot. The following commands are assumed to be run as root.

First, install exim:

    apt-get install exim4

Yay, we've installed the exim mail service. Now we need to reconfigure it for our needs:

    dpkg-reconfigure exim4-config

Select "internet site" and type in the FQDN of the server (whatever.com) when prompted. Do **not** split config files.

Now we're going to enable using the "address_pipe" which allows routing emails to scripts:

    echo "SYSTEM_ALIASES_PIPE_TRANSPORT = address_pipe" > /etc/exim4.conf.localmacros

Because we've made a config change, you have to run:

    update-exim4.conf

Okay now we'll add an "alias" that'll forward mail to a script:

    nano /etc/aliases

At the bottom of this, enter something in this format:

    address:|/path/to/script

That means messages to address@whatever.com will be piped to that script as STDIN.

You can change that "address" to whatever you want as long as it doesn't contain any spaces, I believe. Has to resolve into a valid email address.

Make sure `/path/to/script` is `chmod +x` and in a readable folder and anything it refers to has an absolute path.

    service exim4 restart

Not sure if you really need to restart exim4, but do it anyway just to be sure.

To see if it works, send a test email to that address, via a command like:

    echo "This is a test." | mail -s Testing script@whatever.com

And check out the exim4 log to see if it routes to your script correctly:

    tail -f /var/log/exim4/mainlog

That's it!

## Using PHP CLI

To use a PHP script to intercept mail and parse it and do stuff with it:

    apt-get install php5-cli php5-dev php-pear

Once that's installed, run:

    pecl install mailparse
    echo "extension=mailparse.so" > /etc/php5/cli/conf.d/mailparse.ini

Next, download and use this awesome helper class: [MimeMailParser](https://code.google.com/p/php-mime-mail-parser/)

See example script `mail-test.php` in this repo to see how to use it.

## "Returned 127" Error

If you see an error like this: `Child process of address_pipe transport returned 127 (could mean unable to exec or command does not exist) from command`

It probably means that either the interpreter for the script isn't accessible, or the script itself isn't accessible to everybody.
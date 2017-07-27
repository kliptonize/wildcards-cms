<?php

return [
    // cockpit session name
    'session.name' => 'wilcards',
/*
    // salt for password hashing etc.
    'sec-key'      => 'c3b40c4c-db44-s5h7-a814-b4931a15e5e1',
*/
    // default system language
    'i18n'         => 'en',

    // use mongodb as main data storage
    "database"    => [
        "server"  => "mongodb://localhost:27017",
        "options" => ["db" => "wildcards"]
    ],
/*
    // mailer smtp settings
    "mailer"            => [
        "from"      => "info@mydomain.tld",
        "transport" => "smtp",
        "host"      => "",
        "user"      => "",
        "password"  => "xxxxxx",
        "port"      => 25,
        "auth"      => true,
        "encryption"=> ""    # '', 'ssl' or 'tls'
    ]
*/
];

/*
 * You'll probably fuck up installing MongoDB for PHP7.x, here's a chain of commands to run:
 * (cd to the /bin folder of your php-installation)
    $ pecl download mongodb
    $ tar zxvf mongodb*.tgz
    $ cd mongodb*
    $ phpize
    $ ./configure --with-openssl-dir=/usr/local/opt/openssl
    $ make
    $ sudo make install
    $ echo "extension=mongodb.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`;
 * After that, manually copy-paste mongodb.so from 'bin/mongodb-*' to 'lib/php/extensions/no-debug-*'
 * restart MAMP / server
*/
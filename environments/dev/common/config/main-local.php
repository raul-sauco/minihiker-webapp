<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            // From running applications access the service name in the network
            'dsn' => 'mysql:host=mhdb:3306;dbname=mhdb',
            'username' => 'mh_user',
            'password' => 'password',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];

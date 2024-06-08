<?php

use Balpom\SymfonyMessengerSqlite\SmsNotification;
use Balpom\SymfonyMessengerSqlite\SmsNotificationHandler;
use Balpom\Messenger\Bridge\Sql\Transport\SqlConnection;
use Balpom\Messenger\Bridge\Sql\Transport\SqlTransport;
use Balpom\Messenger\Bridge\Sql\Transport\SqlReceiver;
use Balpom\Messenger\Bridge\Sql\PDO\SqlitePDO;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\Serialization\PhpSerializer;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

return [
    CacheItemPoolInterface::class => function () {
        return new FilesystemAdapter('test_namespace', 10, __DIR__ . '/../var/cache');
    },
    'sqlite-dsn' => 'sqlite:' . __DIR__ . '/../data/alternative-queue.sqlite',
    PDO::class => function (ContainerInterface $container) {
        $dsn = $container->get('sqlite-dsn');
        return SqlitePDO::create($dsn);
    },
    SqlConnection::class => function (ContainerInterface $container) {
        $pdo = $container->get(PDO::class);
        $configuration = []; // See DEFAULT_OPTIONS in SqlConnection.
        return new SqlConnection($pdo, $configuration);
    },
    // Need for SqlTransport autowiring.
    SerializerInterface::class => function () {
        return new PhpSerializer;
    },
    'sql-async' => function (ContainerInterface $container) {
        return new SqlReceiver($container->get(SqlConnection::class));
    },
    'message-bus' => function (ContainerInterface $container) {
        $handler = new SmsNotificationHandler($container);
        return new MessageBus([
    new SendMessageMiddleware(
            new SendersLocator([
                SmsNotification::class => [SqlTransport::class]
                    ], $container)
    ),
    new HandleMessageMiddleware(
            new HandlersLocator([
                SmsNotification::class => [$handler],
                    ])
    )
        ]);
    }
];

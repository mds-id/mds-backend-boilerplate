<?php

declare(strict_types=1);

use Bluepeer\Core\Dbal\Entity;
use Bluepeer\Core\Dbal\EntityInterface;
use Bluepeer\Core\Inflector\InflectorFactory;
use Bluepeer\Core\Inflector\InflectorFactoryInterface;
use DI\ContainerBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        Connection::class => function (ContainerInterface $container) {
            $config     = Yaml::parseFile(__DIR__ . '/../config/database.yaml');
            $connection = DriverManager::getConnection([
                'dbname'   => $config['database']['schema'],
                'user'     => $config['database']['username'],
                'password' => $config['database']['password'],
                'host'     => $config['database']['host'],
                'driver'   => $config['database']['driver']
            ]);
            return $connection;
        },
        EntityInterface::class => function (ContainerInterface $container) {
            return new Entity(
                $container->get(Connection::class),
                $container->get(InflectorFactoryInterface::class)
            );
        },
        InflectorFactoryInterface::class => function (ContainerInterface $container) {
            return new InflectorFactory();
        }
    ]);
};

<?php

declare(strict_types=1);

namespace App\Infrastructure\Bus;

use App\Application\QueryHandlerInterface;
use App\Application\QueryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QueryBus implements ServiceSubscriberInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * @throws ExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function query(QueryInterface $query): mixed
    {
        $queryHandlerClass = $query::class.'Handler';
        if (!$this->container->has($queryHandlerClass)) {
            throw new \Error('QueryHandler doesnt exists'.$queryHandlerClass);
        }

        $this->logger->info('Query Called:'.$query::class);

        /** @var QueryHandlerInterface $handler */
        $handler = $this->container->get($queryHandlerClass);

        $responseHandler = $handler->handle($query);
        $serializer = new Serializer(
            [
                new DateTimeNormalizer(),
                new UidNormalizer(),
                new ObjectNormalizer(),
            ]
        );

        return $serializer->normalize($responseHandler, 'json');
    }

    public static function getSubscribedServices(): array
    {
        return [
            new SubscribedService(
                'handlers',
                ContainerInterface::class,
                false,
                new AutowireLocator('application.query.handler')
            ),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\HelpersTest;

use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Singleton that gets all relations and always return the same value.
 */
final class GetRelationsSingleton
{
    public static ?GetRelationsSingleton $instance = null;
    public static $values = [];
    private static KernelBrowser $client;

    private function __construct() {}

    public function __clone()
    {
        throw new \BadMethodCallException('Clone is not supported');
    }

    public function __get($name): string
    {
        if (array_key_exists($name, self::$values)) {
            return (string) self::$values[$name];
        }
        $inflector = InflectorFactory::create()->build();
        self::$client->request('GET', '/api/Food/'.$inflector->pluralize($name));
        $response = self::$client->getResponse();
        $result = json_decode($response->getContent(), true)[0]['id'];
        self::$values[$name] = $result;

        return $result;
    }

    public function delete($name): void
    {
        unset(self::$values[$name]);
    }

    public function reset(): void
    {
        self::$instance = null;
    }

    public static function instance(KernelBrowser $browser): self
    {
        if (null === self::$instance) {
            self::$instance = new self();

            $kernel = $browser->getKernel();
            self::$client = $browser;

            if ('test' !== $kernel->getEnvironment()) {
                throw new \LogicException('Primer must be executed in the test environment');
            }
            $inflector = InflectorFactory::create()->build();

            /** @var EntityManagerInterface $entityManager */
            $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

            // Run the schema update tool using our entity metadata
            $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
            exec('php bin/console doctrine:schema:drop --force -n -q --env=test && bin/console doctrine:schema:create --env=test -n -q');

            exec('php bin/console doctrine:fixtures:load --group=test --env=test -n');

            // get ids for all classes
            foreach ($metadatas as $metadata) {
                $className = $metadata->name;
                $shortName = (new \ReflectionClass($className))->getShortName();
                $name = lcfirst($shortName);
                $plural = 'food' === $name ? 'foods' : $inflector->pluralize($name);
                self::$client->request('GET', '/api/Food/'.$plural);
                $response = self::$client->getResponse();

                if ($response->getContent() && $contentArr = json_decode($response->getContent(), true)) {
                    if (isset($contentArr[0]['id'])) {
                        $result = $contentArr[0]['id'];
                        self::$values[$name] = $result;
                    }
                }
            }
        }

        return self::$instance;
    }
}

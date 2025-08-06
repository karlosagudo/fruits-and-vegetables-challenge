<?php

declare(strict_types=1);

namespace App\Infrastructure\Controllers;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OpenApiViewer extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('@Resources/viewer.html.twig');
    }

    public function spec(Request $request): Response
    {
        $openApiSpec = file_get_contents(__DIR__.'/../Resources/openApiSpec.yml');
        if ('' === $openApiSpec || '0' === $openApiSpec || false === $openApiSpec) {
            return new Response(
                'ERROR: '.__DIR__.'/../Resources/openApiSpec.yml does not exists',
                Response::HTTP_NOT_FOUND
            );
        }

        $port = $request->server->getInt('DOCKER_HTTP_PORT', 80);
        $authHost = $request->server->getString('KEYCLOAK_HOST', 'envKeycloakHost');
        $authRealm = $request->server->getString('KEYCLOAK_REALM', 'envKeycloakRealm');
        $clientId = $request->server->getString('KEYCLOAK_CLIENT_ID', 'envKeycloakClientId');
        $host = $request->server->getString('HOST', 'http://localhost');
        $openApiSpec = str_replace('http://localhost:80', $host.':'.$port, $openApiSpec);
        $openApiSpec = str_replace('http://localhost:3000', $host.':'.$port, $openApiSpec);
        $openApiSpec = str_replace('###KEYCLOAK_HOST###', $authHost, $openApiSpec);
        $openApiSpec = str_replace('###KEYCLOAK_REALM###', $authRealm, $openApiSpec);
        $openApiSpec = str_replace('###KEYCLOAK_CLIENT_ID###', $clientId, $openApiSpec);

        return new Response($openApiSpec);
    }

    public function tokenReceiver(): Response
    {
        return $this->render('@Resources/oauth-receiver.html.twig');
    }
}

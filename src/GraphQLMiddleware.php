<?php

namespace Xtwoend\HyGraphQL;

use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Hyperf\Utils\Context;
use Hyperf\Utils\Codec\Json;
use Psr\SimpleCache\CacheInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TheCodingMachine\GraphQLite\SchemaFactory;
use Hyperf\Contract\ConfigInterface;

class GraphQLMiddleware implements MiddlewareInterface
{   
    /**
     * Undocumented variable
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Undocumented function
     *
     * @param ContinerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {   
        if (! $this->isGraphQL($request)) {
            return $handler->handle($request);
        }
        
        $input = $request->getParsedBody();
        $query = $input['query'];

        $schema = $this->schema($request);

        $variableValues = isset($input['variables']) ? $input['variables'] : null;
       
        $result = GraphQL::executeQuery($schema, $query, null, null, $variableValues);
        
        return $this->getResponse()->withBody(new SwooleStream(Json::encode($result)));
    }

    protected function getResponse(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }

    protected function isGraphQL(ServerRequestInterface $request): bool
    {
        return $request->getUri()->getPath() === '/graphql';
    }

    protected function schema($request)
    {
        $container = $this->container;
        $config = $container->get(ConfigInterface::class);
        
        $factory = new SchemaFactory($container->get(CacheInterface::class), $container);

        $factory->addControllerNamespace($config->get('graphql.class_map.controller'));
        $factory->addTypeNamespace($config->get('graphql.class_map.type'));
        
        if($config->get('graphql.mode') == 'dev') {
            $factory->devMode();
        }else{
            $factory->prodMode();
        }

        if($config->get('graphql.guard.authentication')) {
            $auth = $config->get('graphql.guard.authentication');
            $factory->setAuthenticationService(new $auth($request));
        }

        if($config->get('graphql.guard.authorization')) {
            $authorize = $config->get('graphql.guard.authorization');
            $factory->setAuthenticationService(make($authorize));
        }
        
        return $factory->createSchema();
    }
}
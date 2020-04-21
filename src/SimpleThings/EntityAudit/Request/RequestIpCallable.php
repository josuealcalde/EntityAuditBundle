<?php

namespace SimpleThings\EntityAudit\Request;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class RequestIpCallable
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return string|null
     *
     * @throws \Exception
     */
    public function __invoke()
    {
        $requestStack = $this->container->get('request_stack');
        /** @var Request $request */
        $request = $requestStack == null ? null : $requestStack->getCurrentRequest();
        if (null !== $request) {
            return $request->getClientIp();
        }
        return null;
    }
}

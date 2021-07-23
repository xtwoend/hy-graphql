<?php

namespace Xtwoend\HyGraphQL;

use TheCodingMachine\GraphQLite\Security\AuthenticationServiceInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    protected $request;

    public function __construct($request) 
    {
        $this->request = $request;
    }

    public function isLogged(): bool
    {
        $request = $this->request;

        if($request->getAttribute('user')) {
            return true;
        }

        return false;
    }

    public function getUser(): ?object
    {
        return $this->request->getAttribute('user');
    }
}
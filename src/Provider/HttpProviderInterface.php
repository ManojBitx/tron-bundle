<?php

namespace ManojX\TronBundle\Provider;

interface HttpProviderInterface
{
    /**
     * Make a HTTP request
     *
     * @param string $endpoint
     * @param array $payload
     * @param string $method
     *
     * @return array
     */
    public function request(string $endpoint, array $payload = [], string $method = 'GET'): array;
}
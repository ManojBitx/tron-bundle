<?php

namespace ManojX\TronBundle\Node;

class Base
{
    protected function parse(array $response): array
    {
        $error = $this->checkErrors($response);
        if ($error['error']) {
            return [
                'success' => false,
                'error' => $error,
            ];
        }

        return [
            'success' => true,
            'data' => $response
        ];
    }


    private function checkErrors(array $response): array
    {
        $errorMessage = [
            'error' => false,
            'message' => null,
            'rawMessage' => null,
        ];

        if (isset($response['Error'])) {
            $parsedMessage = $this->hexMessageParser($response['message']);
            return array_merge(['error' => true], $parsedMessage);
        }

        if (isset($response['code']) && strpos($response['code'], 'ERROR') !== false) {
            $parsedMessage = $this->hexMessageParser($response['message']);
            return array_merge(['error' => true], $parsedMessage);
        }

        if (isset($response['result']['code']) && strpos($response['result']['code'], 'ERROR') !== false) {
            $parsedMessage = $this->hexMessageParser($response['result']['message']);
            return array_merge(['error' => true], $parsedMessage);
        }

        if (isset($response['result']['message'])) {
            $parsedMessage = $this->hexMessageParser($response['result']['message']);
            return array_merge(['error' => true], $parsedMessage);
        }

        return $errorMessage;

    }

    private function hexMessageParser($message): array
    {
        $message = hex2bin($message);
        $parts = explode(':', $message);

        $messagePart = array_pop($parts);
        $rawPart = implode(':', $parts);

        return [
            'message' => trim($messagePart),
            'rawMessage' => trim($rawPart),
        ];
    }

}

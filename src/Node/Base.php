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
            'type' => null,
            'message' => null
        ];

        if (isset($response['Error'])) {
            $parts = explode(':', $response['Error'], 2);
            if (count($parts) === 2) {
                return [
                    'error' => true,
                    'type' => trim($parts[0]),
                    'message' => trim($parts[1])
                ];
            }
        } else if (isset($response['code']) && $response['code'] === 'SIGERROR') {
            $message = hex2bin($response['message']);
            $parts = explode(':', $message, 3);
            return [
                'error' => true,
                'type' => trim($parts[1]),
                'message' => trim($parts[2])
            ];

        }

        return $errorMessage;

    }
}

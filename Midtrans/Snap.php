<?php

namespace Midtrans;

require_once dirname(__FILE__) . '/Midtrans.php';

class Snap
{
    /**
     * Create Snap payment page
     *
     * @param array $params Payment options
     * @return string Snap token
     * @throws Exception
     */
    public static function getSnapToken($params)
    {
        if (!isset($params['transaction_details'])) {
            throw new \Exception('Missing required transaction_details');
        }

        $apiConfig = Config::getSnapBaseUrl();
        $snapToken = ApiRequestor::post(
            $apiConfig . '/transactions',
            Config::$serverKey,
            $params
        );

        return $snapToken->token;
    }

    /**
     * Create Snap payment page, with this version returning full API response
     *
     * @param array $params Payment options
     * @return object Snap response (token and redirect_url)
     * @throws Exception
     */
    public static function createTransaction($params)
    {
        if (!isset($params['transaction_details'])) {
            throw new \Exception('Missing required transaction_details');
        }

        $apiConfig = Config::getSnapBaseUrl();
        $response = ApiRequestor::post(
            $apiConfig . '/transactions',
            Config::$serverKey,
            $params
        );

        return $response;
    }

    /**
     * Create Snap payment page, with this version returning full API response
     *
     * @param array $params Payment options
     * @return string Snap token
     * @throws Exception
     */
    public static function createTransactionToken($params)
    {
        return static::getSnapToken($params);
    }

    /**
     * Create Snap payment page URL, with this version returning full API response
     *
     * @param array $params Payment options
     * @return string Snap redirect URL
     * @throws Exception
     */
    public static function createTransactionUrl($params)
    {
        $response = static::createTransaction($params);
        return $response->redirect_url;
    }
}

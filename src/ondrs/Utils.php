<?php

namespace ondrs\iDefendApi;


use Kdyby\Curl\Response;
use Nette\Utils\Json;
use Nette\Utils\JsonException;

class Utils
{

    /**
     * @param $data
     * @return mixed
     * @throws iDefendJsonException
     * @throws iDefendException
     */
    public static function jsonDecode($data)
    {
        if ($data instanceof Response) {
            $data = $data->getResponse();
        }

        try {
            $json = Json::decode($data);

            if (isset($json->data->error)) {

                $err = $json->data->error;

                if (is_object($err)) {
                    $msg = [];

                    foreach ($err as $k => $e) {
                        $msg[] = $k . ': ' . (is_array($e) ? join(', ', $e) : $e);
                    }

                    throw new iDefendException(join('; ', $msg));
                } else {
                    throw new iDefendException($err);
                }
            }

            return $json;

        } catch (JsonException $e) {
            throw new iDefendJsonException($e->getMessage());
        }
    }

} 

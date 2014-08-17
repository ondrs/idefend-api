<?php
/**
 * Created by PhpStorm.
 * User: Ondra
 * Date: 22.2.14
 * Time: 18:03
 */

namespace ondrs\iDefendApi;


class iDefendException extends \Exception
{
    const
        AUTH_ERROR = 'Authorisation error',
        NO_POLICY = "The policy couldn't be found";
}


class iDefendCurlException extends iDefendException
{

}


class iDefendJsonException extends iDefendException
{

}

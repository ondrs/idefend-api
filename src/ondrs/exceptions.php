<?php

namespace ondrs\iDefendApi;


class iDefendException extends \Exception
{
    const AUTH_ERROR = 'Authorisation error';
    const NO_POLICY = "The policy couldn't be found";
}


class iDefendCurlException extends iDefendException
{

}


class iDefendJsonException extends iDefendException
{

}

class iDefendFileNotFoundException extends iDefendException
{

}

class iDefendIOException extends iDefendException
{

}

iDefend API [![Build Status](https://travis-ci.org/ondrs/idefend-api.png?branch=master)](https://travis-ci.org/ondrs/idefend-api)
==============

PHP API wrapper for iDefend API.


Instalation
-----

composer.json

    "ondrs/idefend-api": "dev-master"

Usage
-----

    $sender = new ondrs\iDefendApi\Sender();
    $idefend = new ondrs\iDefendApi\iDefend('path/to/temp/dir', $sender);

All methods from API documentation (http://docs.idefend.apiary.io) are implemented with the same name.

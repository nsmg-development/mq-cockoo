<?php

use Lcobucci\JWT\Configuration;

function get_client_id (): int
{
    return (int)Configuration::forUnsecuredSigner()->parser()->parse(request()->bearerToken())->claims()->get('aud')[0];
}

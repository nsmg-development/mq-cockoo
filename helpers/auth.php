<?php

use Lcobucci\JWT\Configuration;

/**
 * @note figure out client credential id from DB By access token using JWT reverse calculate.
 * @return int
 */
function get_client_id (): int
{
    return (int)Configuration::forUnsecuredSigner()->parser()->parse(request()->bearerToken())->claims()->get('aud')[0];
}

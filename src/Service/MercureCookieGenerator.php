<?php

namespace App\Service;

use App\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class MercureCookieGenerator{

    private $secret;

    public function __construct(string $secret){

        $this->secret = $secret;
    }

    public function generate(){

        $token = (new Builder())
            ->set('mercure', ['subscribe' => ['*'], 'publish' => ["*"]])
            ->sign(new Sha256(), $this->secret)
            ->getToken();

        return "mercureAuthorization={$token}; path=mercure; HttpOnly;";
    }
}
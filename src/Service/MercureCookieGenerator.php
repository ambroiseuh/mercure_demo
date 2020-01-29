<?php

namespace App\Service;

use App\Entity\User;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class MercureCookieGenerator{

    private $secret;

    public function __construct(string $secret){

        $this->secret = $secret;
    }

    public function generate(){

        $token = (new Builder())
            ->withClaim('mercure',['subscribe' => ['*'], 'publish' => ["*"]])
            ->getToken(new Sha256(), new Key($this->secret));

        return "mercureAuthorization={$token}; path=mercure; HttpOnly;";
    }
}
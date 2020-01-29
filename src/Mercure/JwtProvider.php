<?php


namespace App\Mercure;


use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class JwtProvider
{

    /**
     * @var string
     */
    private $secret;

    public function __construct(string $secret){

        $this->secret = $secret;
    }

    public function __invoke(): string{


        return (new Builder())
            ->withClaim('mercure',['subscribe' => ['*'], 'publish' => ["*"]])
            ->getToken(new Sha256(), new Key($this->secret));
    }
}
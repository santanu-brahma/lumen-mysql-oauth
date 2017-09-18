<?php
namespace Santanu\Lumen_Oauth\Models;

class Client extends Base
{
    protected $table = 'oauth_clients';

    public static function findByClientId($client_id)
    {
        return self::where(['client_id' => $client_id])->first();
    }

    public function getSecret()
    {
        return $this->client_secret;
    }
}

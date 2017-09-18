<?php

namespace Santanu\Lumen_Oauth\Storage;

use Illuminate\Database\Eloquent\Model;
use Santanu\Lumen_Oauth\Models\Client;
use Santanu\Lumen_Oauth\Interfaces\Oauthable;

use OAuth2\Storage\ClientCredentialsInterface;
use OAuth2\Storage\UserCredentialsInterface;
use OAuth2\Storage\AccessTokenInterface;
use OAuth2\Storage\RefreshTokenInterface;

class MySQL implements
    AccessTokenInterface,
    ClientCredentialsInterface,
    UserCredentialsInterface,
    RefreshTokenInterface
{

    /** @var Model $model */
    private $userModel;
    private $user;

    public function __construct(Oauthable $userModel, array $config = [])
    {
        $this->userModel = $userModel;
    }

    public function setUser($username, $password)
    {
        $this->userModel->username = $username;
        $this->userModel->password = $this->userModel->getPasswordHasher()->make($password);
        return boolval($this->userModel->save());
    }

    /* UserCredentialsInterface */
    public function checkUserCredentials($username, $password)
    {		
        if (($user = $this->getUser($username)) &&
            $this->userModel->getPasswordHasher()->check($password, $user->getPassword())
        ) {
            $this->user = $user;

            return true;
        }
        return false;
    }

    public function getUserDetails($username)
    {
        $user = $this->getUser($username)->toArray();
        $user['user_id'] = $user[$this->userModel->getKeyName()];
        return $user;
    }

    /* ClientCredentialsInterface */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        if ($result = Client::findByClientId($client_id)) {
            return $client_secret == $result['client_secret'];
        }
        return false;
    }

    public function isPublicClient($client_id)
    {
        if (!$result = Client::findByClientId($client_id)) {
            return false;
        }
        return empty($result['client_secret']);
    }

    /* ClientInterface */
    public function getClientDetails($client_id)
    {
        $result = Client::findByClientId($client_id);
        return is_null($result) ? false : $result;
    }

    public function setClientDetails(
        $client_id,
        $client_secret = null,
        $redirect_uri = null,
        $grant_types = null,
        $scope = null,
        $user_id = null
    ) {
        if ($this->getClientDetails($client_id)) {
            $this->collection('client_table')->update(
                ['client_id' => $client_id],
                [
                    '$set' => [
                        'client_secret' => $client_secret,
                        'redirect_uri' => $redirect_uri,
                        'grant_types' => $grant_types,
                        'scope' => $scope,
                        'user_id' => $user_id,
                    ],
                ]
            );
        } else {
            $client = [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri,
                'grant_types' => $grant_types,
                'scope' => $scope,
                'user_id' => $user_id,
            ];
            $this->collection('client_table')->insert($client);
        }
        return true;
    }

    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types'])) {
            $grant_types = explode(' ', $details['grant_types']);

            return in_array($grant_type, $grant_types);
        }
        // if grant_types are not defined, then none are restricted
        return true;
    }

    /* AccessTokenInterface */
    public function getAccessToken($access_token)
    {
        $token = null;
        if (is_null($this->user)) {
            $user = $this->userModel->findByAccessToken($access_token);
            if ($user) {
                $token = $user->getAccessToken($access_token);
            }
        } else {
            $token = $this->user->getAccessToken($access_token);
        }
        return is_null($token) ? false : $token;
    }

    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        // if it exists, update it.
        if ($token = $this->getAccessToken($access_token)) {
            $token->client_id = $client_id;
            $token->expires = $expires;
            $token->scope = $scope;
        } else {
            ;
            $token = [
                'access_token' => $access_token,
                'client_id' => $client_id,
                'expires' => $expires,
                'scope' => $scope,
            ];
        }
        $this->user->setAccessToken($token)->save();
        return true;
    }

    public function unsetAccessToken($access_token)
    {
        $this->user->deleteAccessToken($access_token)->save();
    }


    /* RefreshTokenInterface */
    public function getRefreshToken($refresh_token)
    {
        $token = null;
        if (is_null($this->user)) {
            $user = $this->userModel->findByRefreshToken($refresh_token);
            if ($user) {
                $this->user = $user;
                $token = $user->getRefreshToken($refresh_token);
            }
        } else {
            $token = $this->user->getRefreshToken($refresh_token);
        }
        return is_null($token) ? false : $token;
    }

    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        $token = [
            'refresh_token' => $refresh_token,
            'client_id' => $client_id,
            'expires' => $expires,
            'scope' => $scope,
        ];
        $this->user->setRefreshToken($token)->save();
        return true;
    }

    public function unsetRefreshToken($refresh_token)
    {
        //$this->user->deleteRefreshToken($refresh_token)->save();
        return true;
    }

    public function getUser($data)
    {
        $result = (strpos($data, '@') === false) ? $this->userModel->getUserByUsername($data) : $this->userModel->getUserByEmail($data);
        return is_null($result) ? false : $result;
    }

    public function getClientKey($client_id, $subject)
    {
        $result = $this->collection('jwt_table')->findOne([
            'client_id' => $client_id,
            'subject' => $subject,
        ]);
        return is_null($result) ? false : $result['key'];
    }

    public function getClientScope($client_id)
    {
        if (!$clientDetails = $this->getClientDetails($client_id)) {
            return false;
        }
        if (isset($clientDetails['scope'])) {
            return $clientDetails['scope'];
        }
        return null;
    }
}

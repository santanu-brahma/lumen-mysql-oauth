<?php
namespace Santanu\Lumen_Oauth\Traits;

use Illuminate\Hashing\BcryptHasher;
use Santanu\Lumen_Oauth\Models\AccessToken;
use Santanu\Lumen_Oauth\Models\RefreshToken;

trait Oauthable
{
    protected function getArrayableRelations() {
        return array_merge(parent::getArrayableRelations(), [
            'access_tokens' => $this->accessTokens,
            'refresh_tokens' => $this->refreshTokens,
        ]);
    }

    public function getUserByUsername($username) {
        return $this->where(['username' => $username])->first();
    }
	
    public function getUserByEmail($email) {
        return $this->where(['email' => $email])->first();
    }

    public function getPassword() {
        return $this->password;
    }

    public function getPasswordHasher() {
        return (new BcryptHasher());
    }

    public function findByAccessToken($access_token) {
		$accessToken = AccessToken::where('access_token', $access_token);
		if($accessToken->exists())
			return self::find($accessToken->first())->first();
		else
			return null;
    }

    public function findByRefreshToken($refresh_token) {
		$refreshToken = RefreshToken::where('refresh_token', $refresh_token);
		if($refreshToken->exists())
			return self::find($refreshToken->first())->first();
		else
			return null;
    }

    public function accessTokens() {
		return $this->hasMany('Santanu\Lumen_Oauth\Models\AccessToken', 'user_id');
    }

    public function getAccessToken($access_token) {
        return $this->accessTokens()->where('access_token', $access_token)->first();
    }

    public function setAccessToken($data) {
        $token = ($data instanceof AccessToken) ? $data : new AccessToken($data);
		$this->accessTokens()->save($token);
        return $this;
    }

    public function deleteAccessToken($access_token) {
		$this->accessTokens()->delete($this->getAccessToken($access_token));
        return $this;
    }

    public function refreshTokens() {
		return $this->hasMany('Santanu\Lumen_Oauth\Models\RefreshToken', 'user_id');
    }

    public function getRefreshToken($refresh_token) {
        return $this->refreshTokens()->where('refresh_token', $refresh_token)->first();
    }

    public function setRefreshToken($data) {
        $token = ($data instanceof RefreshToken) ? $data : new RefreshToken($data);
		$this->refreshTokens()->save($token);
        return $this;
    }

    public function deleteRefreshToken($refresh_token) {
		$this->refreshTokens()->delete($this->getRefreshToken($refresh_token));
        return $this;
    }
}

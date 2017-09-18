<?php
namespace Santanu\Lumen_Oauth\Interfaces;

interface Oauthable
{

    public function getUserByUsername($username);
    public function getUserByEmail($email);

    public function getPassword();
    public function getPasswordHasher();

    public function findByAccessToken($access_token);
    public function accessTokens();
    public function getAccessToken($access_token);
    public function setAccessToken($data);
    public function deleteAccessToken($access_token);

    public function refreshTokens();
    public function getRefreshToken($refresh_token);
    public function setRefreshToken($data);
    public function deleteRefreshToken($refresh_token);
}

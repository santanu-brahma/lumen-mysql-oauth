<?php
namespace Santanu\Lumen_Oauth\Models;

/**
 * Class Token
 * @package Santanu\Lumen_Oauth\Models
 */
class RefreshToken extends Base
{
	protected $table = 'oauth_refreshes';
	
    protected $fillable = ['user_id', 'client_id', 'refresh_token', 'expires', 'scope'];
}

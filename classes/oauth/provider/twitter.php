<?php
/**
 * OAuth Twitter Provider
 *
 * Documents for implementing Twitter OAuth can be found at
 * <http://dev.twitter.com/pages/auth>.
 *
 * [!!] This class does not implement the Twitter API. It is only an
 * implementation of standard OAuth with Twitter as the service provider.
 *
 * @package    Kohana/OAuth
 * @category   Provider
 * @author     Kohana Team
 * @copyright  (c) 2010 Kohana Team
 * @license    http://kohanaframework.org/license
 * @since      3.0.7
 */

namespace OAuth;

class OAuth_Provider_Twitter extends OAuth_Provider {

	public $name = 'twitter';
	
	public $uid_key = 'user_id';

	public function url_request_token()
	{
		return 'https://api.twitter.com/oauth/request_token';
	}

	public function url_authorize()
	{
		return 'https://api.twitter.com/oauth/authenticate';
	}

	public function url_access_token()
	{
		return 'https://api.twitter.com/oauth/access_token';
	}
	
	public function get_user_info(OAuth_Consumer $consumer, OAuth_Token $token)
	{		
		// Create a new GET request with the required parameters
		$request = OAuth_Request::factory('resource', 'GET', 'http://api.twitter.com/1/users/lookup.json', array(
			'oauth_consumer_key' => $consumer->key,
			'oauth_token' => $token->token,
			'user_id' => $token->uid,
		));

		// Sign the request using the consumer and token
		$request->sign($this->signature, $consumer, $token);

		$user = current(json_decode($request->execute()));
		
		// Create a response from the request
		return array(
			'nickname' => $user->screen_name,
			'name' => $user->name ?: $user->screen_name,
			'location' => $user->location,
			'image' => $user->profile_image_url,
			'description' => $user->description,
			'urls' => array(
			  'Website' => $user->url,
			  'Twitter' => 'http://twitter.com/'.$user->screen_name,
			),
			'credentials' => array(
				'uid' => $token->uid,
				'provider' => $this->name,
				'token' => $token->token,
				'secret' => $token->secret,
			),
		);
	}

} // End OAuth_Provider_Twitter
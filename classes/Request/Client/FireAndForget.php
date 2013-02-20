<?php
/**
 * [Request_Client_External] Fire and Forget driver performs external requests
 * using the without waiting for response from the server.
 *
 * @package    Kohana
 * @category   FireAndForget
 * @author     Kohana Team
 * @copyright  (c) 2013 Michał Musiał
 */
class Request_Client_FireAndForget extends Request_Client_External {
	/**
	 * Sends the HTTP message [Request] to a remote server and processes
	 * the response.
	 *
	 * @param   Request   $request  request to send
	 * @param   Response  $request  response to send
	 * @return  Response
	 */
	public function _send_message(Request $request, Response $response)
	{
		return $response;
    }
}

<?php
/**
 * [Request_Client_External] Fire and Forget driver performs external requests
 * using the fsocketopen() function without the need to wait for response from
 * the server.
 *
 * @package    Kohana
 * @category   FireAndForget
 * @author     Michał Musiał
 * @copyright  (c) 2013 Michał Musiał
 * @license    http://kohanaframework.org/license
 */
class Request_Client_FireAndForget extends Request_Client_External {

    /**
     * @var int Request timeout in settings
     */
    protected $_timeout = 30;

    /**
     * Getter and setter for the connection timeout property.
     *
     * @param type $timeout The connection timeout, in seconds.
     * @return Request_Client
     */
    public function timeout($timeout)
    {
		if ($timeout === NULL)
			return $this->_timeout;

		$this->_timeout = $timeout;

		return $this;
    }

    /**
	 * Sends the HTTP message [Request] to a remote server and processes
	 * the response.
	 *
	 * @param   Request   $request  request to send
	 * @param   Response  $request  response to retrieve and return
	 * @return  Response
	 */
	public function _send_message(Request $request, Response $response)
	{
        $url_parts = parse_url($request->uri());
        $ssl = (Arr::get($url_parts, 'scheme') === 'https');
        $host = $url_parts['host'];
        $port = ($ssl) ? Arr::get($url_parts, 'port', 443) : Arr::get($url_parts, 'port', 80);

        // Split URL into host and path+query to ensure valid headers
        $request->uri(Arr::get($url_parts, 'path', '/') . URL::query($request->query(), FALSE));
        $request->headers('host', $host . (($ssl AND $port != 443 OR ! $ssl AND $port != 80) ? ':' . $port : ''));

        // Make sure we're telling the server we're closing the connection
        // After all, it's Fire and *Forget*
        $request->headers('connection', 'Close');

        // Build and make the request
        $http_request = $request->render();

        try
        {
            $fp = fsockopen(
                    (($ssl) ? 'ssl://' . $host : $host),
                    $port,
                    $errno,
                    $errstr,
                    $this->_timeout);
        }
        catch (Exception $e)
        {
            // Silently log the error and return
            Kohana::$log->add(Log::WARNING, Kohana_Exception::text($e));
            return $response;
        }

        fwrite($fp, $http_request);
        fclose($fp);

        // Return unmodified $response
        return $response;
    }
}

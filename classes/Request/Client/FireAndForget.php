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
        $url_parts = parse_url($request->uri());
        $ssl = (Arr::get($url_parts, 'scheme') === 'https');
        $host = $url_parts['host'];
        $port = ($ssl) ? Arr::get($url_parts, 'port', 443) : Arr::get($url_parts, 'port', 80);
        $http_body = '';

        if (in_array($request->method(), array(HTTP_Request::POST, HTTP_Request::PUT)))
        {
            if ( ! ($http_body = $request->body()))
            {
                $http_body = http_build_query($request->post());
                $request->body($http_body);

                // Make sure there is a Content-Type set
                if ( ! $request->header('Content-Type'))
                {
                    $request->header('Content-Type', 'application/x-www-form-urlencoded');
                }
            }

            $request->headers('Content-Length', strlen($http_body));
        }

        // Make sure we're telling the server we're closing the connection
        $request->headers('Connection', 'Close');

        $http_headers = array(
            $request->method() . ' ' . Arr::get($url_parts, 'path', '/') . ' ' . HTTP::$protocol,
            'Host: ' . $host . (($ssl AND $port != 443 OR ! $ssl AND $port != 80) ? ':' . $port : ''),
        );

        foreach ($request->headers() as $key => $value)
        {
            $http_headers[] = $key . ': ' . $value;
        }

        // Build HTTP Request
        $http_request = implode("\r\n", $http_headers) . "\r\n\r\n";

        if ( ! empty($http_body))
        {
            $http_request .= $http_body;
        }

        // Make the request
        if ($ssl)
        {
            $host = 'ssl://' . $host;
        }

        try
        {
            $fp = fsockopen($host, $port);
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

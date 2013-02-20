Kohana Fire and Forget Request Module
=====================================

Ever wanted to make an external request in Kohana and not to worry about the
response thus speeding up your application? This is the module for you!

This module adds a Fire and Forget External Request Client which you can use when making external requests.
It utilises [`fsocketopen()`](http://www.php.net/fsockopen) function which allows making a request without the need wait for a response, thus considerably speeding up your app's execution time.

You will find this module useful if your application makes simple API calls and its execution doesn't rely on the response returned.


## Features

- Allows "asynchronous" requests from PHP.
- Considerable speed increase compared to Kohana's native External Request Clients.
- Extends native Kohana's features, so you don't need to learn any new syntax.
- Full support for SSL requests.

## Installation

1. Checkout/download files and folders to `MODPATH/fafrequest`.
2. Add this entry under `Kohana::modules` array in your `APPPATH/bootstrap.php`:

```php
'fafrequest'   => MODPATH.'fafrequest',    // Fire and Forget Request
```

## Configuration

This module requires no configuration.


## Usage examples

In order to use Fire and Forget Request Client you either have to specify it as a default one:

```php
Kohana_Request_Client_External::$client = 'Request_Client_FireAndForget';
```

or you need to specify the client with each request (see examples below).


### Simple GET request

```php
Request::factory('http://example.com/')
        ->client(new Request_Client_FireAndForget())
        ->execute();
```

### GET with a Query String

```php
Request::factory('http://example.com/example')
        ->client(new Request_Client_FireAndForget())
        ->query('foo', 'bar')
        ->query('baz', 'qux')
        ->execute();
```

**NOTE**: You always have to provide the GET parameters using [`Request::query()`](http://kohanaframework.org/3.3/guide-api/Request#query) method and not in the Request URI provided when calling [`Request::factory()`](http://kohanaframework.org/3.3/guide-api/Request#factory) method.

### Simple POST request

```php
Request::factory('http://example.com/submit')
        ->client(new Request_Client_FireAndForget())
        ->method(HTTP_Request::POST)
        ->post('foo', 'bar')
        ->post('baz', 'qux')
        ->execute();
```

### Mixing both GET and POST request parameters

```php
Request::factory('http://example.com/submit')
        ->client(new Request_Client_FireAndForget())
        ->method(HTTP_Request::POST)
        ->post('foo', 'bar')
        ->post('baz', 'qux')
        ->query('corge', 'grault')
        ->query('garply', 'waldo')
        ->execute();
```

### Setting Request Timeout

It is possible to set the [`fsocketopen()`](http://www.php.net/fsockopen) timeout parameter.
The default value is 30 seconds.

```php
Request::factory('http://example.com/')
        ->client(new Request_Client_FireAndForget(array('timeout' => 10)))
        ->execute();
```

### SSL Requests

You can make secure requests as simple as you would make a normal ones, just use `https` scheme:

```php
Request::factory('https://example.com/')
        ->client(new Request_Client_FireAndForget())
        ->execute();
```


## Notes

Be aware that you can only use this module when making an [External Request](http://kohanaframework.org/3.3/guide/kohana/requests#external-requests)!

If an error occures when opening a socket the script will not stop the script's execution, instead it will log an error message. This is by design - after all it is "Fire and *Forget*".


## Acknowledgements

The code for this module is losely based on [W-Shadow's script](http://w-shadow.com/blog/2007/10/16/how-to-run-a-php-script-in-the-background/) and is inspired by [my mate's](https://twitter.com/diggersworld) [question on StackOverflow](http://stackoverflow.com/questions/14587514/php-fire-and-forget-post-request).

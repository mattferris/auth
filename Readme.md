Auth
====

An exstensible authentication library for PHP.

    use MattFerris\Auth\Authenticator;

    $auth = new Authenticator();
    $auth->register($authProvider);
    $response = $auth->authenticate($request);

Auth Providers
--------------

Must implement `ProviderInterface`.

    use MattFerris\Auth\ProviderInterface;
    use MattFerris\Auth\RequestInterface;
    use MattFerris\Auth\Response;

    class Provider implements ProviderInterface
    {
        public function provides()
        {
            return [
                'handlers' => [
                    'PasswordRequest' => [$this, 'passwordAuth'],
                    'PrivateKeyRequest' => function (RequestInterface $request) {
                        // do stuff here
                    }
                ],
                'manipulators' => [
                    'PasswordResponse' => [$this, 'manipulatePasswordResponse']
                ]
            ];
        }

        public function passwordAuth(RequestInterface $request)
        {
            $response = new Response(false);

            if ($this->db->verify($request->getUsername(), $request->getPassword()) {
                $response = new Response(true, $this->generateToken(
                    array(
                        'username' => $request->getUsername(),
                        'uid' => $userId,
                        'expiry' => time() + 3600
                    )
                ));
            }
            return $response;
        }
    }

Handlers
--------

Handlers provide mechanisms for authenticating requests. Handlers for a particular request are processed in the order they were registered. If a handler returns a response no further handlers are processed. Handlers can return either null (no response), or an instance of `ResponseInterface`.

Manipulators
------------

Manipulators allow for the modification of responses. As with handlers, manipulators are processed in the order the were registered, with processing stopping when a response is returned. Manipulators allow for more sophisticated responses to be generated without complicating the authentication itself. For example, users can login to your service via multiple remote services (Google, Facebook, etc..) and then are issued a login token. You could register handlers for each remote service:

    namespace MyApp;

    use MattFerris\Auth\ProviderInterface

    class RemoteServicesProvider implements ProviderInterface
    {
        public function provides()
        {
            return [
                'handlers' => [
                    'MyApp\\GoogleRequest' => [$this, 'handleGoogleRequest'],
                    'MyApp\\FacebookRequest' => [$this, 'handleFacebookRequest']
                ]
            ];
        }

        public function handleGoogleRequest(GoogleRequest $request)
        {
            // call google auth api
            return $this->generateToken(...);
        }

        public function handleFacebookRequest(FacebookRequset $request)
        {
            // call facebook auth api
            return $this->generateToken(...);
        }

        protected function generateToken(...)
        {
            // blah, blah, blah
        }
    }

Alternatively, you could decouple the token generation from the authentication using a manipulator.

    namespace MyApp;

    use MattFerris\Auth\ProviderInterface;
    use MattFerris\Auth\Response;

    class TokenManiplatorProvider implements ProviderInterface
    {
        public function provides()
        {
            return [
                'manipulators' => [
                    'MattFerris\\Auth\\Response' => [$this, 'manipulateResponse']
                ]
            ];
        }

        public function manipulateResponse(Response $response)
        {
            // don't manipulate failed auth requests
            if (!$response->isValid()) {
                return;
            }

            $tokenData = $response->getAttributes();
            $token = $this->generateToken($tokenData);
            $response = new Response(true, array('token' => $token));
        }
    }

This manipulator can take any valid auth request and generate a response with a token. Handlers no longer need to know how to generate tokens. Your application code looks like:

    namespace MyApp;

    $auth = new MattFerris\Auth\Authenticator();
    $auth
        ->register(new RemoteServicesProvider())
        ->register(new TokenManipulatorProvider());

    $response = $auth->authenticate(new GoogleRequest());
    setCookie('auth_token', $response->getAttribute('token'));

Requests
--------

Must implement `RequestInterface`. Providers register to handle various requests. Specific types of requests have unique implementations defined by their own interfaces. For example, `PasswordRequest` implements `PasswordRequestInterface` and provides two additional methods: `getUsername()` and `getPassword()`.

    interface PasswordRequestInterface extends RequestInterface
    {
        public function getUsername();
        public function getPassword();
    }

    class PasswordRequest implements PasswordRequestInterface
    {
        protected $username;
        protected $password;

        public function __construct($username, $password)
        {
            $this->username = $username;
            $this->password = $password;
        }

        public function getUsername()
        {
            return $this->username;
        }

        public function getPassword()
        {
            return $this->password;
        }
    }

    $response = $auth->authenticate(new PasswordRequest($username, $password));

Responses
---------

Must implement `ResponseInterface`. Has one method, `isValid()`. `TokenResponse` accepts two arguments, `status` and `token`; has one extra method `getToken()`.

    $response = new TokenResponse($status, $token);

    if ($response->isValid()) {
        echo $response->getToken();
    }

Auth
====

An extensible authentication library for PHP.

    use MattFerris\Auth\Authenticator;

    $auth = new Authenticator();
    $auth->register($authProvider);
    $response = $auth->authenticate($request);

Requests
--------

Every authentication request must be an instance of `RequestInterface`. Handlers subscribe to different event types based on the request's class. For example, a handler may subscribe to `MattFerris\Auth\PasswordRequest`, and will only be called when an instance of `MattFerris\Auth\PasswordReqeust` is received.

    $auth->authenticate(new MattFerris\Auth\PasswordRequest(...));

Providers
---------

Providers register handlers and manipulators which handle authentication requests, and manipulate authentication responses (respectively).

    use MattFerris\Provider\ProviderInterface;
    use MattFerris\Auth\RequestInterface;
    use MattFerris\Auth\ResponseInterface;
    use MattFerris\Auth\Response;

    class Provider implements ProviderInterface
    {
        public function provides($consumer)
        {
            // $consumer contains an instance of Authenticator
            $authenticator = $consumer;

            $authenticator
                ->handle('PassowrdRequest', [$this, 'passwordAuth'])
                ->handle('PrivateKeyRequest', function (RequestInterface $request) {
                    // do private key stuff here
                });

            $authentcator
                ->manipulate('PasswordResponse',  [$this, 'manipulatePasswordResponse']);
        }
    }

Handlers
--------

Handlers provide mechanisms for authenticating requests. Handlers for a particular request are processed in the order they were registered. If a handler returns a response no further handlers are processed. Handlers can return either null (no response), or an instance of `ResponseInterface`.

In the above example, `$this->passwordAuth` was defined as a handler. It's implementation might look like:

    public function passwordAuth(RequestInterface $request)
    {
        // prepare an auth-failed response by default
        $response = new Response(false);

        // verify the username and password against the database
        if ($this->db->verify($request->getUsername(), $request->getPassword()) {

            // everything matched, so generate an auth-success response, including
            // the username, uid and auth expiry
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

Responses
---------

Handlers return instances of `ResponseInterface` that contain the status of the authentication request and any additional information provided by the handler. The status of the response can be determined by calling `isValid()`.

    $response = $auth->authenticate($request);
    if ($response->isValid()) {
        echo 'success';
    } else {
        echo 'failed';
    }

Manipulators
------------

Manipulators allow for the modification of responses. As with handlers, manipulators are processed in the order the were registered, with processing stopping when a response is returned. Manipulators allow for more sophisticated responses to be generated without complicating the authentication itself.

For example, users can login to your service via multiple remote services (Google, Facebook, etc..) and then be issued a login token. You could register handlers for each remote service:

    namespace MyApp;

    use MattFerris\Provider\ProviderInterface

    class RemoteServicesProvider implements ProviderInterface
    {
        public function provides($consumer)
        {
            $consumer
                ->handle('MyApp\\GoogleRequest', [$this, 'handleGoogleRequest'])
                ->handle('MyApp\\FacebookRequest', [$this, 'handleFacebookRequest']);
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
            // token generation stuff
        }
    }

Alternatively, you could decouple the token generation from the authentication using a manipulator.

    namespace MyApp;

    use MattFerris\Privder\ProviderInterface;
    use MattFerris\Auth\Response;

    class TokenManiplatorProvider implements ProviderInterface
    {
        public function provides($consumer)
        {
            $consumer->manipulate('MattFerris\\Auth\\Response', [$this, 'manipulateResponse']);
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

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
                'PasswordRequest' => 'passwordAuth'
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

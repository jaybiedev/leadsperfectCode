<?php
namespace Library;

class SessionManager  {

    protected $privatizer, $name, $cookie;
    const GLOBAL_NAMESPACE = '__SYSTEM_GLOBAL_SESSION__';

    public function __construct($name = null, $privatizer=null,  $cookie = [])
    {
        if (empty($name)) {
            $parsedUrl = parse_url($_SERVER['HTTP_HOST']);

            $host = explode('.', $parsedUrl['path']);
            $name = $host[0];

            if (empty($name) || is_numeric($name))
                $name = self::GLOBAL_NAMESPACE;
        }

        $this->privatizer = $privatizer;
        $this->name = $name;
        $this->cookie = $cookie;

        $this->cookie += [
            'lifetime' => 0,
            'path'     => ini_get('session.cookie_path'),
            'domain'   => ini_get('session.cookie_domain'),
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly' => true
        ];

        $this->Setup();
    }

    public static function Start($on_file=false)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {

            if ($on_file) {
                $session_path = $this->getSessionPath();
                ini_set('session.save_handler', 'files');
                session_set_save_handler($session, true);
                session_save_path($session_path);
            }
            session_start();
        }
        return;
    }

    protected function Setup()
    {
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);

        session_name($this->name);

        session_set_cookie_params(
            $this->cookie['lifetime'], $this->cookie['path'],
            $this->cookie['domain'], $this->cookie['secure'],
            $this->cookie['httponly']
        );
    }

    public function Destroy()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        unset($_SESSION);

        /*
        setcookie(
            $this->name, '', time() - 42000,
            $this->cookie['path'], $this->cookie['domain'],
            $this->cookie['secure'], $this->cookie['httponly']
        );
        */
        return session_destroy();
    }

    public function Refresh()
    {
        return session_regenerate_id(true);
    }

    public function get($name)
    {
        $result = null;
        if (!empty($this->privatizer)) {
            if (isset($_SESSION[$this->privatizer][$name]))
                $result = $_SESSION[$this->privatizer][$name];
        }
        else {
            if (isset($_SESSION[$name]))
                $result = $_SESSION[$name];
        }

        return $result;
    }

    public function put($name, $value)
    {
        if (!empty($this->privatizer))
            $_SESSION[$this->privatizer][$name] = $value;
        else
            $_SESSION[$name] = $value;

        return $this->Get($name);
    }


    public function has($name)
    {

        if (!empty($this->privatizer))
            return isset($_SESSION[$this->privatizer][$name]);
        else
            return isset($_SESSION[$name]);
    }

    public function remove($name)
    {
        if (!empty($this->privatizer))
            unset($_SESSION[$this->privatizer][$name]);
        else
            unset($_SESSION[$name]);

        return true;
    }


    private function getSessionpath()
    {
        $path = APPPATH . '/sessions';
        if (!is_dir($path))
            throw new Exception("Session Path not found.");

        return $path;
    }
}

<?php
session_start();
class Session
{
    private $sessionState = false;                      // The state of the session
    private static $instance;                           // THE only instance of the class

    public static function getInstance()
    {
        if(!isset(self::$instance)) {
            self::$instance = new self;
        }
        self::$instance->startSession();
        return self::$instance;
    }

    public function startSession()
    {
        // if($this->sessionState == false) {
        //     if (session_status() == PHP_SESSION_NONE) {
        //     } else {
        //         $this->sessionState = session_start();
        //     }
        // }
        return $this->sessionState;
    }

    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function __get($name)
    {
        if(isset($_SESSION[$name])) {
          return $_SESSION[$name];
        } else {
          echo "<script>window.location.href = 'index.php?err=1';</script>";
        }
    }

    public function destroy()
    {
        // if($this->sessionState == true) {
            $this->sessionState = !session_destroy();
            unset($_SESSION);
            return !$this->sessionState;
        // }
        // return false;
    }
}

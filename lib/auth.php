<?php if (!defined("GUARD")) die("Directly not accessible!"); ?>

<?php 

function redirect($page) {
  header("Location: {$page}");
  exit();
}

class Auth {
    private $user_storage;
    private $user = NULL;
  
    public function __construct(UserStorage $user_storage) {
      $this->user_storage = $user_storage;

      if (isset($_SESSION["user"])) {
        $this->user = $this->user_storage->findById($_SESSION["user"]["id"]);
        $_SESSION["user"] = $this->user;
      }
    }
  
    public function register($data) {
      $user = [
        'username'  => $data['username'],
        'email'  => $data['email'],
        'password'  => password_hash($data['password'], PASSWORD_DEFAULT),
        "money"     => 1000,
        "isadmin"   => false,
        "requests" => [],
      ];
      $id = $this->user_storage->add($user);
      $user["id"] = $id;
      $this->login($user);
      return $id;
    }
  
    public function user_exists($username) {
      $users = $this->user_storage->findOne(['username' => $username]);
      return !is_null($users);
    }
  
    public function authenticate($username, $password) {
      $users = $this->user_storage->findMany(function ($user) use ($username, $password) {
        return $user["username"] === $username && 
               password_verify($password, $user["password"]);
      });
      return count($users) === 1 ? array_shift($users) : NULL;
    }
    
    public function is_authenticated() {
      return !is_null($this->user);
    }
  
    public function authorize($roles = []) {
      if (!$this->is_authenticated()) {
        return FALSE;
      }
      foreach ($roles as $role) {
        if (in_array($role, $this->user["roles"])) {
          return TRUE;
        }
      }
      return FALSE;
    }
  
    public function login($user) {
      $this->user = $user;
      unset($_SESSION["pager"]);
      $_SESSION["user"] = $user;
    }
  
    public function logout() {
      $this->user = NULL;
      unset($_SESSION["user"]);
      unset($_SESSION["pager"]);
    }
  
    public function authenticated_user() {
      return $this->user;
    }

    public function is_admin() {
      return $this->user["isadmin"];
    }
  }

?>
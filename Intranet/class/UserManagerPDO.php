<?php


/** same thing as ItemManagerPDO, it is just SQL queries for both user and patient table as patient inherits of user*/
class UserManagerPDO
{

    protected $db;
    protected $lastInsertId;

    const HASH = 'sha512';
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** We check if the email is already used */

    public function hash($password, $email){

        $lenght = strlen($password);
        $sel1 = ($lenght*12+7)^2;
        $sel2_tmp = $sel1*42+3;
        $sel2 = hash('sha384', $sel2_tmp.$email );

        return hash(self::HASH, $sel1.$password.$sel2.$email);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function ifExists(User $user)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_user WHERE Email = :email');
        $req->bindValue(':email',$user->getEmail());
        $req->execute();

        if($req->fetch())
            return true;
        else
            return false;
    }

    public function ifUserExists($id)
    {
        $req = $this->selectUser($id);

        if($req->fetch())
            return true;
        else
            return false;
    }

    /** functions to return data in the table user order by last name or just one user selected by id */
    /**
     * @return false|PDOStatement
     */
    public function selectAllUser()
    {
        $req = $this->db->query('SELECT * FROM tbl_user ORDER BY LName');
        return $req;
    }

    /**
     * @param $id
     * @return bool|PDOStatement
     */
    public function selectUser($id)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_user WHERE ID = :id');
        $req->bindValue(':id',$id);
        $req->execute();
        return $req;
    }

    /**
     * Register a new user in the table user
     * @param User $user
     */
    public function register(User $user)
    {
        $req = $this->db->prepare('INSERT INTO tbl_user(FName, LName, Email, Password, Status)
        VALUE (:FName, :LName,  :Email, :Password, :Status)');
        $req->bindValue(':FName', $user->getFName());
        $req->bindValue(':LName', $user->getLName());
        $req->bindValue(':Email', $user->getEmail());
        $req->bindValue(':Password', $this->hash($user->getPassword(), $user->getEmail()));
        $req->bindValue(':Status', $user->getStatus());
        $req->execute();
        $req->closeCursor();
    }

        /** delete a user */
    public function deleteUser($id)
    {
        $req = $this->db->prepare('DELETE FROM tbl_user WHERE ID = :id');
        $req->bindValue(':id',$id);
        $req->execute();
        $req->closeCursor();
    }

        /** update a user in the table */
    public function updateUser(User $User)
    {
        $req = $this->db->prepare('
          UPDATE tbl_user
          SET FName = :FName, LName = :LName, Email = :Email, Status = :Status
          WHERE ID = :id');
        $req->bindValue(':id', $User->getId());
        $req->bindValue(':FName', $User->getFName());
        $req->bindValue(':LName', $User->getLName());
        $req->bindValue(':Email', $User->getEmail());
        $req->bindValue(':Status', $User->getStatus());
        $req->execute();
        $req->closeCursor();

        /** If the password has changed, we check and if it is the case, we hash it before sending in the database */
        if(!empty($User->getPassword()))
        {
            $reqPassword = $this->db->prepare('UPDATE tbl_user SET Password = :Password WHERE ID = :ID');
            $reqPassword->bindValue(':ID', $User->getId());
            $reqPassword->bindValue(':Password', $this->hash($User->getPassword(), $User->getEmail()));
            $reqPassword->execute();
            $reqPassword->closeCursor();
        }
    }

    /**
     * check the information in order to login, if it is the case, it creates a new user with its information
     * @param $name
     * @param $surname
     * @param $email
     * @param $password
     * @return null|User
     */
    public function login($email, $password)
    {
        $login = $this->db->prepare('SELECT * FROM tbl_user WHERE Email = :email AND Password = :password');// verif des informations
        $login->execute(array(
            'email' => $email,
            'password' => $password
        ));

        if($userInformation = $login->fetch())
        {
            $user = new User(array(
                'id' => $userInformation['ID'],
                'fName' => $userInformation['FName'],
                'lName' => $userInformation['LName'],
                'email'  => $userInformation['Email'],
                'password' => $userInformation['Password'],
                'status'  => $userInformation['Status'],
            ));
            $login->closeCursor();
            return $user;
        }
        else
        {
            return NULL;
        }
    }

    /** GETTER */
    public function getLastInsertId()
    {
        $req = $this->db->query('SELECT MAX(ID) AS maxId FROM tbl_user');
        $lastId = $req->fetch();
        $req->closeCursor();
        return $lastId['maxId'];
    }
}


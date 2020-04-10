<?php

class User
{


    private $id,
            $status;

    protected   $fName,
                $lName,
                $email,
                $password,
                $passwordConfirmed,
                $errors = [];

    const NOT_SAME_PASSWORD = 1;
    const INVALID_F_NAME = 2;
    const INVALID_L_NAME = 3;
    const INVALID_EMAIL = 4;
    const INVALID_PASSWORD = 5;
    const INVALID_SIZE_L_NAME = 6;
    const INVALID_SIZE_F_NAME = 8;
    const INVALID_SIZE_EMAIL_NAME = 9;
    const INVALID_EMAIL_FORMAT = 9;

    public function __construct($values = [])
    {
        if (!empty($values))
        {
            $this->hydrate($values);
        }
    }

    /**
     * @param $data array
     * @return void
     */
    public function hydrate($data)
    {
        foreach ($data as $attribute => $values)
        {
            $method = 'set'.ucfirst($attribute);

            if (is_callable([$this, $method]))
            {
                $this->$method($values);
            }
        }
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
    }

    /** Display the object user */
    public function description()
    {
        $output = "Id = ". $this->id ."<br>";
        $output = $output . "First Name = ". $this->fName ."<br>";
        $output = $output . "Last Name = ". $this->lName."<br>";
        $output = $output . "Email = ". $this->email."<br>";
        $output = $output . "Password = ". $this->password."<br>";
        $output = $output . "Status = ". $this->status."<br>";

        return $output;
    }

    public function checkSamePassword()
    {
        if($this->password != $this->passwordConfirmed)
        {
            $this->errors[] = self::NOT_SAME_PASSWORD;
            return false;
        }
        else
        {
            return true;
        }
    }

    public function isValidUpdate()
    {
        $valid = true;
        if(!empty($this->password))
        {
            if(!$this->checkSamePassword())
            {
                $valid = false;
            }
        }
        if(empty($this->lName) || empty($this->fName) || empty($this->email))
        {
            $valid = false;
        }

        if($valid)
            return true;
        else
            return false;
    }

    public function isValid()
    {
        $valid = true;
        if(!$this->checkSamePassword())
        {
            $valid = false;
        }
        if(empty($this->fName) || empty($this->lName) || empty($this->email) || empty($this->password))
        {
            $valid = false;
        }

        if($valid == true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    /**Getter*/

    public function getId()
    {
        return $this->id;
    }

    public function getLName()
    {
        return $this->lName;
    }

    public function getFName()
    {
        return $this->fName;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getPasswordConfirmed()
    {
        return $this->passwordConfirmed;
    }

    /** Setter */

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * @param $lName
     */
    public function setLName($lName)
    {
        if(empty($lName) || !is_string($lName))
        {
            $this->errors[] = self::INVALID_L_NAME;
        }
        elseif (strlen($lName) > 50){
            $this->errors[] = self::INVALID_SIZE_L_NAME;
        }
        else
        {
            $this->lName = $lName ;
        }
    }

    public function setFName($fName)
    {
        if(empty($fName) || !is_string($fName))
        {
            $this->errors[] = self::INVALID_F_NAME;
        }
        elseif (strlen($fName) > 50){
            $this->errors[] = self::INVALID_SIZE_F_NAME;
        }
        else
        {
            $this->fName = $fName;
        }
    }

    /**
     * @param $email
     */
    public function setEmail($email)
    {
        if(empty($email) || !is_string($email))
        {
            $this->errors[] = self::INVALID_EMAIL;
        }
        elseif (strlen($email) > 255){
            $this->errors[] = self::INVALID_SIZE_EMAIL_NAME;
        }
        elseif (!preg_match( " /^[^\W][a-zA-Z0-9_.]+(\.[a-zA-Z0-9_.]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/ " , $email ))
        {
            $this->errors[] = self::INVALID_EMAIL_FORMAT;
        }
        else
        {
            $this->email = $email;
        }
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        if(empty($password))
        {
            $this->errors[] = self::INVALID_PASSWORD;
        }
        else
        {
            $this->password = $password;
        }
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param $passwordConfirmed
     */
    public function setPasswordConfirmed($passwordConfirmed)
    {
        $this->passwordConfirmed = $passwordConfirmed;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }
}

<?php


class Film
{

    /**declaration of the attributes*/
    private $id,
        $nom,
        $dateDeSortie,
        $genre,
        $pictureName,
        $pictureTmpName,
        $picturePath,
        $realisateur,
        $duree,
        $acteur,
        $synopsis,
        $errors = [];


    const INVALID_DESCRIPTION = 1;
    const INVALID_EXTENSION = 2;
    const FAIL_MOVE_PICTURE = 3;
    const INVALID_NAME = 4;
    const INVALID_DATE = 5;
    const INVALID_GENRE = 6;
    const INVALID_REALISATEUR = 7;
    const INVALID_DUREE = 8;
    const INVALID_ACTEUR = 9;
    const INVALID_SYNOPSIS = 10;

    const FILE_PATH_ROOT = 'images/affiches/';
    const FILE_PATH = '../images/affiches/';

    /**the function constructor */
    public function __construct($values = [])
    {
        $this->picturePath = "unknown";
        if (!empty($values)) {
            $this->hydrate($values);
        }
    }

    /**
     * @param $data array
     * @return void
     */

    /**function used to call the setters */
    public function hydrate($data)
    {
        foreach ($data as $attribute => $values) {
            $method = 'set' . ucfirst($attribute);
            /** Call the setters of the attributes in the constructor */

            if (is_callable([$this, $method])) {
                $this->$method($values);
            }
        }
    }

    /**functions "isValid..." checks the information before sending to the database */
    public function isValid()
    {
        if (empty($this->nom) || empty($this->dateDeSortie) || empty($this->genre) || empty($this->realisateur) || empty($this->duree) || empty($this->acteur) || empty($this->synopsis))
            return false;
        else
            return true;
    }

    /* A modifier
    public function isValidUpdateSeance($quantity)
    {
        if (empty($this->quantity) || empty($this->id))
            return false;
        else {
            if ($this->checkEnoughItem($quantity))
                return true;
            else
                return false;
        }
    }*/

    /**
     * Check if the file respect the chart
     * @return bool
     */
    public function isValidPictureFormat()
    {
        $allowedExtension = array('png', 'gif', 'jpg', 'jpeg');

        $extension = substr(strrchr($this->pictureName, '.'), 1);

        if (!in_array($extension, $allowedExtension)) {
            $this->errors[] = self::INVALID_EXTENSION;
            return false;
        }
        return true;
    }

    /**
     * Move the upload image in the folder
     * @return bool
     */

    /** We get the extension of the picture, we rename it, and put it in the folder, else it returns an error */
    public function movePicture()
    {
        $extension = substr(strrchr($this->pictureName, '.'), 1);

        $newName = basename($this->id . '.' . $extension);

        $newNames = basename($newName);

        $this->pictureName = $newNames;

        $this->picturePath = self::FILE_PATH_ROOT . $this->pictureName;

        if (file_exists(self::FILE_PATH_ROOT)) {
            return move_uploaded_file($this->pictureTmpName, self::FILE_PATH_ROOT . $newNames);
        } else if (file_exists(self::FILE_PATH)) {
            return move_uploaded_file($this->pictureTmpName, self::FILE_PATH . $newNames);
        } else {
            $this->errors[] = self::FAIL_MOVE_PICTURE;
            return false;
        }

    }


    /**
     * @return bool
     */

    /** check the picture condition and then add it */
    public function addPicture()
    {
        if ($this->isValidPictureFormat()) {
            if ($this->movePicture())
                return true;
            else
                return false;
        } else {
            return false;
        }
    }

    /**
     * @param mixed $id
     */
    /**SETTERS: if information are not valid it fills the array errors*/
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    /**
     * @param mixed $itemDesc
     */

    public function setItemDesc($itemDesc)
    {
        if (empty($itemDesc) || !is_string($itemDesc))
            $this->errors[] = self::INVALID_DESCRIPTION;
        else
            $this->itemDesc = $itemDesc;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        if(empty($nom) || !is_string($nom))
            $this->errors[] = self::INVALID_NAME;
        else
            $this->nom = $nom;
    }

    /**
     *
     *
     *  Les setters sont à faire
     *
     */
    /**
     * @param mixed $dateDeSortie
     */
    public function setDateDeSortie($dateDeSortie)
    {
        $this->dateDeSortie = $dateDeSortie;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    /**
     * @param mixed $pictureName
     */
    public function setPictureName($pictureName)
    {
        $this->pictureName = $pictureName;
    }

    /**
     * @param mixed $pictureTmpName
     */
    public function setPictureTmpName($pictureTmpName)
    {
        $this->pictureTmpName = $pictureTmpName;
    }

    /**
     * @param string $picturePath
     */
    public function setPicturePath($picturePath)
    {
        $this->picturePath = $picturePath;
    }

    /**
     * @param mixed $realisateur
     */
    public function setRealisateur($realisateur)
    {
        $this->realisateur = $realisateur;
    }

    /**
     * @param mixed $duree
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;
    }

    /**
     * @param mixed $acteur
     */
    public function setActeur($acteur)
    {
        $this->acteur = $acteur;
    }

    /**
     * @param mixed $synopsis
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;
    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     *
     * Getters
     *
     */

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @return mixed
     */
    public function getDateDeSortie()
    {
        return $this->dateDeSortie;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @return mixed
     */
    public function getPictureName()
    {
        return $this->pictureName;
    }

    /**
     * @return mixed
     */
    public function getPictureTmpName()
    {
        return $this->pictureTmpName;
    }

    /**
     * @return string
     */
    public function getPicturePath()
    {
        return $this->picturePath;
    }

    /**
     * @return mixed
     */
    public function getRealisateur()
    {
        return $this->realisateur;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @return mixed
     */
    public function getActeur()
    {
        return $this->acteur;
    }

    /**
     * @return mixed
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
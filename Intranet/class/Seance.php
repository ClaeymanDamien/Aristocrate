<?php


class Seance
{
    private
        $idFilm,
        $idSalle,
        $salle,
        $film,
        $date,
        $horaire,
        $errors = [];

    const INVALID_DATE = 1;
    const INVALID_HOUR = 2;
    const INVALID_ID_FILM = 3;
    const INVALID_ID_SALLE =4;

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
        foreach ($data as $attribute => $values) {
            $method = 'set' . ucfirst($attribute);

            if (is_callable([$this, $method])) {
                $this->$method($values);
            }
        }
    }


    public function isValid()
    {
        if (empty($this->idSalle) || empty($this->idFilm) || empty($this->date) || empty($this->horaire)){
            return false;
        }
        else{
            return true;
        }
    }
    /**
     * Setters
     */


    /**
     * @param mixed $idFilm
     */
    public function setIdFilm($idFilm)
    {
        if(empty($idFilm)){
            $this->errors[] = self::INVALID_ID_FILM;
        }
        else{
            $this->idFilm = $idFilm;
        }
    }

    /**
     * @param mixed $idSalle
     */
    public function setIdSalle($idSalle)
    {
        if(empty($idSalle)){
            $this->errors[] = self::INVALID_ID_SALLE;
        }
        else{
            $this->idSalle = $idSalle;
        }
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        if (!preg_match( " /([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/ " , $date ))
        {
            $this->errors[] = self::INVALID_DATE;
        }
        else{
            $this->date = $date;
        }
    }

    /**
     * @param mixed $film
     */
    public function setFilm($film)
    {
        $this->film = $$this->film;
    }

    /**
     * @param mixed $salle
     */
    public function setSalle($salle)
    {
        $this->salle = $salle;
    }

    /**
     * @param mixed $horaire
     */
    public function setHoraire($horaire)
    {
        if (!preg_match( " /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/ " , $horaire ))
        {
            $this->errors[] = self::INVALID_HOUR;
        }
        else{
            $this->horaire = $horaire;
        }

    }

    /**
     * @param array $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Getters
     */

    /**
     * @return mixed
     */
    public function getIdFilm()
    {
        return $this->idFilm;
    }

    /**
     * @return mixed
     */
    public function getIdSalle()
    {
        return $this->idSalle;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getHoraire()
    {
        return $this->horaire;
    }

    /**
     * @return mixed
     */
    public function getFilm()
    {
        return $this->film;
    }

    /**
     * @return mixed
     */
    public function getSalle()
    {
        return $this->salle;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }


}
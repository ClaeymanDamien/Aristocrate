<?php


class Seance
{
    private
        $idFilm,
        $idSalle,
        $salle,
        $film,
        $date,
        $horraire;

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

    /**
     * Setters
     */


    /**
     * @param mixed $idFilm
     */
    public function setIdFilm($idFilm)
    {
        $this->idFilm = $idFilm;
    }

    /**
     * @param mixed $idSalle
     */
    public function setIdSalle($idSalle)
    {
        $this->idSalle = $idSalle;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param mixed $film
     */
    public function setFilm($film)
    {
        $this->film = $film;
    }

    /**
     * @param mixed $salle
     */
    public function setSalle($salle)
    {
        $this->salle = $salle;
    }

    /**
     * @param mixed $horraire
     */
    public function setHorraire($horraire)
    {
        $this->horraire = $horraire;
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
    public function getHorraire()
    {
        return $this->horraire;
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



}
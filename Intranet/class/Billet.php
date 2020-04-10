<?php


class Billet
{
    private
        $idBillet,
        $idSeance,
        $idTarif,
        $idUser;

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
     * @param mixed $idBillet
     */
    public function setIdBillet($idBillet)
    {
        $this->idBillet = (int) $idBillet;
    }

    /**
     * @param mixed $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = (int) $idUser;
    }

    /**
     * @param mixed $idSeance
     */
    public function setIdSeance($idSeance)
    {
        $this->idSeance = (int) $idSeance;
    }

    /**
     * @param mixed $idTarif
     */
    public function setIdTarif($idTarif)
    {
        $this->idTarif = (int) $idTarif;
    }

    /**
     * Getters
     */


    /**
     * @return mixed
     */
    public function getIdSeance()
    {
        return $this->idSeance;
    }

    /**
     * @return mixed
     */
    public function getIdTarif()
    {
        return $this->idTarif;
    }

    /**
     * @return mixed
     */
    public function getIdBillet()
    {
        return $this->idBillet;
    }

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->idUser;
    }
    
}
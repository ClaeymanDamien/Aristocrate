<?php


class Place
{
    private
        $idBillet,
        $idTarif,
        $quantite;

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
     * @param mixed $idTarif
     */
    public function setIdTarif($idTarif)
    {
        $this->idTarif = $idTarif;
    }

    /**
     * @param mixed $idBillet
     */
    public function setIdBillet($idBillet)
    {
        $this->idBillet = $idBillet;
    }

    /**
     * @param mixed $quantite
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
    }

    /**
     * Getters
     */

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
    public function getIdTarif()
    {
        return $this->idTarif;
    }

    /**
     * @return mixed
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

}
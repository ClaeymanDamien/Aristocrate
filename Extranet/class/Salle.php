<?php


class Salle
{
    private
        $id,
        $nbrPlaces;

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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $nbrPlaces
     */
    public function setNbrPlaces($nbrPlaces)
    {
        $this->nbrPlaces = $nbrPlaces;
    }

    /**
     * Getters
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
    public function getNbrPlaces()
    {
        return $this->nbrPlaces;
    }
}
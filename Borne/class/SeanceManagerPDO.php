<?php


class SeanceManagerPDO
{
    protected $db;

    /**Constructor
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function selectAllSeance()
    {
        return $this->db->query('SELECT * FROM tbl_seance');
    }

    public function selectSeance($idFilm, $idSalle, $idDate, $idHorraire)
    {
        $req = $this->db->prepare('
            SELECT * 
            FROM tbl_seance 
            WHERE IDFilm = :idFilm AND IDSalle = :idSalle AND Date = :date AND Horaire = :horaire
            ORDER BY Horaire');
        $req->bindValue(':idFilm', $idFilm );
        $req->bindValue(':idSalle', $idSalle );
        $req->bindValue(':date', $idDate );
        $req->bindValue(':horaire', $idHorraire );
        $req->execute();

        return $req;
    }

    public function selectSeanceID($id)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_seance WHERE IDSeance = :id');
        $req->bindValue(':id', $id);
        $req->execute();

        return $req;
    }

    /**
     * Date format "Y-m-d"
     * Exemple :SELECT * FROM tbl_seance WHERE IDFilm = 1 AND Date = "2020-02-23" ORDER BY Horaire
     */

    public function selectDaySeances($idFilm, $idDate)
    {
        $req = $this->db->prepare('
            SELECT * 
            FROM tbl_seance 
            WHERE IDFilm = :idFilm  AND Date = :date
            ORDER BY Horaire');
        $req->bindValue(':idFilm', $idFilm );
        $req->bindValue(':date', $idDate );
        $req->execute();

        return $req;
    }


}
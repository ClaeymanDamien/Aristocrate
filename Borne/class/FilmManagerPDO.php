<?php

class FilmManagerPDO
{
    protected $db;

    /**Constructor
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**We take information from the database*/
    public function selectAllFilm()
    {
        return $this->db->query('SELECT * FROM tbl_film');
    }
    
    public function selectIndexFilm($status)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_film WHERE Status = :status ORDER BY ID DESC LIMIT 0, 4');
        $req->bindValue(':status', $status);
        $req->execute();

        return $req;
    }

    public function selectStatusFilm($status)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_film WHERE Status = :status ORDER BY ID DESC');
        $req->bindValue(':status', $status);
        $req->execute();

        return $req;
    }

    public function selectFilm($id)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_film WHERE ID = :id');
        $req->bindValue(':id', $id);
        $req->execute();

        return $req;
    }

    /**We add an film in the database*/
    public function add(Film $film)
    {
        $req = $this->db->prepare('INSERT INTO tbl_film(StockFilm, FilmDesc, FilmPic, FilmMinimum) VALUE (:stockFilm, :filmDesc, :filmPic, :filmMinimum)');
        $req->bindValue(':stockFilm', $film->getStockQuantity());
        $req->bindValue(':filmDesc', $film->getFilmDesc());
        $req->bindValue(':filmPic', $film->getPicturePath());
        $req->bindValue(':filmMinimum', $film->getMinFilm());
        $req->execute();
        $req->closeCursor();
    }

    /**We delete an film from the database */
    public function delete($id)
    {
        $req = $this->db->prepare('DELETE FROM tbl_film WHERE ID = :id');
        $req->bindValue(':id', $id);
        $req->execute();
        $req->closeCursor();
    }

    /**We update an film in the database */
    public function update(Film $film)
    {
        $req = $this->db->prepare('UPDATE tbl_film SET StockFilm = :stockFilm, FilmDesc = :filmDesc, FilmMinimum = :filmMinimum WHERE ID = :id');
        $req->bindValue('stockFilm', $film->getStockQuantity());
        $req->bindValue('filmDesc', $film->getFilmDesc());
        $req->bindValue('filmMinimum', $film->getMinFilm());
        $req->bindValue('id', $film->getID());
        $req->execute();
        $req->closeCursor();

        /** if picture is upload */
        if (!empty($film->getPictureName())) {
            $reqPicture = $this->db->prepare('UPDATE tbl_film SET FilmPic = :filmPic WHERE ID = :id');
            $reqPicture->bindValue('filmPic', $film->getPicturePath());
            $reqPicture->bindValue('id', $film->getID());
            $reqPicture->execute();
            $reqPicture->closeCursor();
        }
    }

    /** GETTER */
    public function getLastInsertId()
    {
        $req = $this->db->query('SELECT MAX(ID) AS maxId FROM tbl_film');
        $lastId = $req->fetch();
        $req->closeCursor();
        return $lastId['maxId'];
    }
}
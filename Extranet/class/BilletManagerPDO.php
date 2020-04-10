<?php


class BilletManagerPDO
{
    protected $db;

    /**Constructor
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function selectAllBillet()
    {
        return $this->db->query('SELECT * FROM tbl_billet');
    }

    public function selectBillet($idBillet)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_billet WHERE IDBillet = :idBillet');
        $req->bindValue(':id', $idBillet);
        $req->execute();

        return $req;
    }

    public function selectBilletUser($idUser)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_billet WHERE IDUser = :idUser');
        $req->bindValue(':idUser', $idUser);
        $req->execute();

        return $req;
    }

    public function billetDeLaSeance($idSeance)
    {
        $req = $this->db->prepare('SELECT IDBillet FROM tbl_billet WHERE IDSeance = :idSeance');
        $req->bindValue(':idSeance', $idSeance);
        $req->execute();

        return $req;
    }


    public function add(Billet $billet)
    {
        $req = $this->db->prepare('INSERT INTO tbl_billet(IDBillet, IDSeance, IDUser) VALUE (:IDBillet, :IDSeance, :IDUser)');
        $req->bindValue(':IDBillet', $billet->getIdBillet());
        $req->bindValue(':IDSeance', $billet->getIdSeance());
        $req->bindValue(':IDUser', $billet->getIdUser());
        $req->execute();
        $req->closeCursor();
    }

    /** GETTER */
    public function getLastInsertId()
    {
        $req = $this->db->query('SELECT MAX(IDBillet) AS maxId FROM tbl_billet');
        $lastId = $req->fetch();
        $req->closeCursor();
        return $lastId['maxId'];
    }

}
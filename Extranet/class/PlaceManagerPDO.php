<?php


class PlaceManagerPDO
{
    protected $db;

    /**Constructor
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function selectAllPlace()
    {
        return $this->db->query('SELECT * FROM tbl_place');
    }

    public function selectPlace($idBillet, $idTarif)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_place WHERE IDBillet = :idBillet AND IDTarif = :idTarif');
        $req->bindValue(':idBillet', $idBillet);
        $req->bindValue(':idTarif', $idTarif);
        $req->execute();

        return $req;
    }

    public function selectPlaceBillet($idBillet)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_place WHERE IDBillet = :idBillet');
        $req->bindValue(':idBillet', $idBillet);
        $req->execute();

        return $req;
    }

    public function nbrDePlace($idBillet)
    {
        $req = $this->db->prepare('SELECT SUM(Quantite) FROM tbl_place WHERE IDBillet = :idBillet');
        $req->bindValue(':idBillet', $idBillet);
        $req->execute();

        return $req;
    }


    public function add(Place $place)
    {
        $req = $this->db->prepare('INSERT INTO tbl_place(IDBillet, IDTarif, Quantite) VALUE (:IDBillet, :IDTarif, :Quantite)');
        $req->bindValue(':IDBillet', $place->getIdBillet());
        $req->bindValue(':IDTarif', $place->getIdTarif());
        $req->bindValue(':Quantite', $place->getQuantite());
        $req->execute();
        $req->closeCursor();
    }


}
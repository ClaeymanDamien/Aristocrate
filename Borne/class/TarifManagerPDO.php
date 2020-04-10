<?php


class TarifManagerPDO
{
    protected $db;

    /**Constructor
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function selectAllTarif()
    {
        return $this->db->query('SELECT * FROM tbl_tarif');
    }

    public function selectTarif($id)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_tarif WHERE ID = :id');
        $req->bindValue(':id', $id);
        $req->execute();

        return $req;
    }
}
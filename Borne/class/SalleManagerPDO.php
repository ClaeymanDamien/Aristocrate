<?php


class SalleManagerPDO
{
    protected $db;

    /**Constructor
     * @param PDO $db
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function selectAllSalle()
    {
        return $this->db->query('SELECT * FROM tbl_salle');
    }
    
    public function selectSalle($id)
    {
        $req = $this->db->prepare('SELECT * FROM tbl_salle WHERE IDSalle = :id');
        $req->bindValue(':id', $id);
        $req->execute();

        return $req;
    }
}
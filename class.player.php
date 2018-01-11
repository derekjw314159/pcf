<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/pcf/dbconfig.php';

class PLAYER
{
	private $conn;

	public function __construct() {
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
		}

    public function __destruct() {
        //$this->conn = null;
		}

    /*
     * Add new Record
     *
     * @param $first_name
     * @param $last_name
     * @param $email
     * @return $mixed
     * */
    public function Create($first_name, $last_name, $email, $dob, $county) {
		try {
			$stmt = $this->conn->prepare("INSERT INTO tbl_players (playerFirstName, playerLastName, playerEmail1, playerDOB, playerCounty)
				VALUES (:first_name,:last_name,:email,:dob,:county)");
			$stmt->bindParam(":first_name", $first_name, PDO::PARAM_STR);
			$stmt->bindParam(":last_name", $last_name, PDO::PARAM_STR);
			$stmt->bindParam(":email", $email, PDO::PARAM_STR);
			$stmt->bindParam(":dob", $dob, PDO::PARAM_STR);
			$stmt->bindParam(":county", $county, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt;
			}
		catch(PDOException $ex) {
			echo $ex->getMessage();
			}
		}

    /*
     * Read all records
     *
     * @return $mixed
     * */
    public function Read() {
		try {
			$stmt = $this->conn->prepare("SELECT * FROM tbl_players");
			$stmt->execute();
			$data = array();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$data[] = $row;
				}
			return $data;
			}	
		catch(PDOException $ex) {
			echo $ex->getMessage();
			}
		}

    /*
     * Delete Record
     *
     * @param $user_id
     * */
    public function Delete($user_id)
    {
        $query = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $query->bindParam("id", $user_id, PDO::PARAM_STR);
        $query->execute();
    }

    /*
     * Update Record
     *
     * @param $first_name
     * @param $last_name
     * @param $email
     * @return $mixed
     * */
    public function Update($first_name, $last_name, $email, $user_id)
    {
        $query = $this->db->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email  WHERE id = :id");
        $query->bindParam("first_name", $first_name, PDO::PARAM_STR);
        $query->bindParam("last_name", $last_name, PDO::PARAM_STR);
        $query->bindParam("email", $email, PDO::PARAM_STR);
        $query->bindParam("id", $user_id, PDO::PARAM_STR);
        $query->execute();
    }

    /*
     * Get Details
     *
     * @param $user_id
     * */
    public function Details($user_id)
    {
        $query = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $query->bindParam("id", $user_id, PDO::PARAM_STR);
        $query->execute();
        return json_encode($query->fetch(PDO::FETCH_ASSOC));
    }
}

?>

<?php

require_once 'dbconfig.php';

class USER {	

	private $conn;
	
	public function __construct() {
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
		}
	
	public function runQuery($sql) {
		$stmt = $this->conn->prepare($sql);
		return $stmt;
		}
	
	public function lasdID() {
		$stmt = $this->conn->lastInsertId();
		return $stmt;
		}
	
	public function register($uname,$email,$upass,$code, $county) {
		try {							
			$password = md5($upass);
			$stmt = $this->conn->prepare("INSERT INTO tbl_users(userName,userEmail,userPass,tokenCode,userCounty) 
			                                             VALUES(:user_name, :user_mail, :user_pass, :active_code, :county)");
			$stmt->bindparam(":user_name",$uname);
			$stmt->bindparam(":user_mail",$email);
			$stmt->bindparam(":user_pass",$password);
			$stmt->bindparam(":active_code",$code);
			$stmt->bindparam(":county",$county);
			$stmt->execute();	
			$res= $stmt;
			}
		catch(PDOException $ex) {
			echo $ex->getMessage();
			}
		// Recover user ID and add to role table
		try {							
			$stmt = $this->conn->prepare("SELECT userID FROM tbl_users WHERE (userEmail=:user_mail)");
			$stmt->bindparam(":user_mail",$email);
			$stmt->execute();
			
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt = $this->conn->prepare("INSERT INTO tbl_roles (roleUser, roleCounty, roleRole) 
				VALUES (:role_user, :role_county, 'P')");
			$stmt->bindparam(":role_user",$row['userID']);
			$stmt->bindparam(":role_county",$county);
			$stmt->execute();
			return $row['userID'];
			}
		catch(PDOException $ex) {
			echo $ex->getMessage();
			}
		}
	
	public function login($email,$upass) {
		try {
			$stmt = $this->conn->prepare("SELECT * FROM tbl_users WHERE userEmail=:email_id");
			$stmt->execute(array(":email_id"=>$email));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);

			#
			/* rowCount logic is not safe
			 * Database forces email to be unique, so safe to assume
			 * a maximum of one record returned
			 */
			// if($stmt->rowCount() == 1) {
			if($userRow !== FALSE) {
				if($userRow['userStatus']=="Y") {
					if($userRow['userPass']==md5($upass)) {
						$_SESSION['userSession'] = $userRow['userID'];
						return true;
						}
					else {
						header("Location: index.php?error");
						exit;
						}
					}	
				else {
					header("Location: index.php?inactive");
					exit;
					}	
				}	
			else {
				header("Location: index.php?error");
				exit;
				}		
			}	
		catch(PDOException $ex) {
			echo $ex->getMessage();
			}
		}
	
	public function is_logged_in() {
		if(isset($_SESSION['userSession'])) {
			return true;
			}
		}
	
	public function redirect($url) {
		header("Location: $url");
		}
	
	public function logout() {
		session_destroy();
		$_SESSION['userSession'] = false;
		}
	
	function send_mail($email,$message,$subject) {						
		require_once('mailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->IsSMTP(); 
		$mail->SMTPDebug  = 2;                     
		$mail->SMTPAuth   = true;                  
		$mail->SMTPSecure = "ssl";                 
		$mail->Host       = "smtp.gmail.com";      
		$mail->Port       = 465;             
		$mail->AddAddress($email);
		#
		// Pull in account details for gmail outside of document root
		require_once(__DIR__ . "/../../gmail.php");
		$mail->Subject    = $subject;
		$mail->MsgHTML($message);
		$mail->Send();
		}	
	}

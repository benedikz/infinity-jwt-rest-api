<?php
	class Customer {
		private $id;
		private $name;
		private $pin;
		private $createdOn;
		private $tableName = 'table_name';
		private $dbConn;

		function setId($id) { $this->id = $id; }
		function getId() { return $this->id; }
		function setName($name) { $this->name = $name; }
		function getName() { return $this->name; }
		function setPin($pin) { $this->pin = $pin; }
		function getPin() { return $this->pin; }
		function setCreatedOn($createdOn) { $this->createdOn = $createdOn; }
		function getCreatedOn() { return $this->createdOn; }

		public function __construct() {
			$db = new DbConnect();
			$this->dbConn = $db->connect();
		}

		public function getAllCustomers() {
			$stmt = $this->dbConn->prepare("SELECT * FROM " . $this->tableName);
			$stmt->execute();
			$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $customers;
		}

		/*public function getCustomerDetailsById() {

			$sql = "SELECT
						c.*,
						u.name as created_user,
						u1.name as updated_user
					FROM customers c
						JOIN users u ON (c.created_by = u.id)
						LEFT JOIN users u1 ON (c.updated_by = u1.id)
					WHERE
						c.id = :customerId";

			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindParam(':customerId', $this->id);
			$stmt->execute();
			$customer = $stmt->fetch(PDO::FETCH_ASSOC);
			return $customer;
		}
        */

		public function insert() {

			$sql = 'INSERT INTO ' . $this->tableName . '(id, name, pin, created_on) VALUES(NULL, :name, :pin, :createdOn)';

			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindParam(':name', $this->name);
			$stmt->bindParam(':pin', $this->pin);
			$stmt->bindParam(':createdOn', $this->createdOn);

			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}

		/*public function update() {

			$sql = "UPDATE $this->tableName SET";
			if( null != $this->getName()) {
				$sql .=	" name = '" . $this->getName() . "',";
			}

			if( null != $this->getAddress()) {
				$sql .=	" address = '" . $this->getAddress() . "',";
			}

			if( null != $this->getMobile()) {
				$sql .=	" mobile = " . $this->getMobile() . ",";
			}

			$sql .=	" updated_by = :updatedBy,
					  updated_on = :updatedOn
					WHERE
						id = :userId";

			$stmt = $this->dbConn->prepare($sql);
			$stmt->bindParam(':userId', $this->id);
			$stmt->bindParam(':updatedBy', $this->updatedBy);
			$stmt->bindParam(':updatedOn', $this->updatedOn);
			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}
		*/

		public function delete() {
			$stmt = $this->dbConn->prepare('DELETE FROM ' . $this->tableName . ' WHERE id = :userId');
			$stmt->bindParam(':userId', $this->id);

			if($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		}
	}
 ?>

<?php
	class Api extends Rest {
		public function __construct() {
			parent::__construct();
		}

		public function generateToken() {
			$name = $this->validateParameter('name', $this->param['name'], STRING);
			$pin = $this->validateParameter('pin', $this->param['pin'], INTEGER);
			try {
				$stmt = $this->dbConn->prepare("SELECT * FROM infinity_users WHERE name = :name AND pin = :pin");
				$stmt->bindParam(":name", $name);
				$stmt->bindParam(":pin", $pin);
				$stmt->execute();
				$user = $stmt->fetch(PDO::FETCH_ASSOC);

				if(!is_array($user)) {
					$this->returnResponse(INVALID_USER_PASS, "Jméno nebo PIN se neshoduje.");
				}

				if( $user['active'] == 0 ) {
					$this->returnResponse(USER_NOT_ACTIVE, "Uživatel je deaktivován.");
				}

				$payload = [
					'iat' => time(),
					'iss' => 'localhost',
					'exp' => time() + (15*60),
					'userId' => $user['id']
				];

				$token = JWT::encode($payload, SECRET_KEY);

				$data = ['token' => $token];
				$this->returnResponse(SUCCESS_RESPONSE, $data);
			} catch (Exception $e) {
				$this->throwError(JWT_PROCESSING_ERROR, $e->getMessage());
			}
		}

    public function register() {
			$name = $this->validateParameter('name', $this->param['name'], STRING, false);
			$pin = $this->validateParameter('pin', $this->param['pin'], INTEGER, false);

			$cust = new Customer;
			$cust->setName($name);
			$cust->setPin($pin);
			$cust->setCreatedOn(date('Y-m-d'));

			if(!$cust->insert()) {
				$message = "Registrace selhala.";
				//$this->returnResponse(REGISTRATION_FAILURE, $message);
				var_dump(http_response_code(200));
			} else {
				$message = "Úspěšně registrován.";
				//$this->returnResponse(SUCCESS_RESPONSE, $message);
				var_dump(http_response_code(404));
			}


		}

		/*
		public function addCustomer() {
			$name = $this->validateParameter('name', $this->param['name'], STRING, false);
			$email = $this->validateParameter('email', $this->param['email'], STRING, false);
			$addr = $this->validateParameter('address', $this->param['address'], STRING, false);
			$mobile = $this->validateParameter('mobile', $this->param['mobile'], INTEGER, false);

			$cust = new Customer;
			$cust->setName($name);
			$cust->setPin($email);
			$cust->setAddress($address);
			$cust->setMobile($mobile);
			$cust->setCreatedBy($this->userId);
			$cust->setCreatedOn(date('Y-m-d'));

			if(!$cust->insert()) {
				$message = 'Failed to insert.';
			} else {
				$message = "Inserted successfully.";
			}

			$this->returnResponse(SUCCESS_RESPONSE, $message);
		}
    */

		public function getCustomerDetails() {
			$customerId = $this->validateParameter('customerId', $this->param['customerId'], INTEGER);

			$cust = new Customer;
			$cust->setId($customerId);
			$customer = $cust->getCustomerDetailsById();
			if(!is_array($customer)) {
				$this->returnResponse(SUCCESS_RESPONSE, ['message' => 'Uživatelská data nenalezena.']);
			}

			$response['id'] 	= $customer['id'];
			$response['name'] 	= $customer['name'];
			$response['pin'] 	= $customer['pin'];
			$this->returnResponse(SUCCESS_RESPONSE, $response);
		}

		public function updateCustomer() {
			$customerId = $this->validateParameter('customerId', $this->param['customerId'], INTEGER);
			$name = $this->validateParameter('name', $this->param['name'], STRING, false);
			$pin = $this->validateParameter('pin', $this->param['pin'], INTEGER, false);

			$cust = new Customer;
			$cust->setId($customerId);
			$cust->setName($name);
			$cust->setPin($pin);

			if(!$cust->update()) {
				$message = 'Aktualizace uživatele se nezdařila.';
			} else {
				$message = "Aktualizace uživatele proběhla úspěšně.";
			}

			$this->returnResponse(SUCCESS_RESPONSE, $message);
		}

		public function deleteCustomer() {
			$customerId = $this->validateParameter('customerId', $this->param['customerId'], INTEGER);

			$cust = new Customer;
			$cust->setId($customerId);

			if(!$cust->delete()) {
				$message = 'Smazání uživatele se nezdařilo.';
			} else {
				$message = "Smazání uživatele proběhlo úspěšně.";
			}

			$this->returnResponse(SUCCESS_RESPONSE, $message);
		}
	}

 ?>

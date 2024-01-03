<?php

namespace common;

use mysqli;

class db_helper
{
    private mysqli $ms;
    private static ?db_helper $db = null;
    private function __construct()
    {
        $this->ms = new mysqli("localhost", "root", "", "edu", 3306);
    }

    public static function get_instance(): ?db_helper
    {
        if (self::$db === null)
            self::$db = new db_helper();
        return self::$db;
    }

    public function add_user(string $login, string $password_hash, string $name): bool{
        if (!isset($login) || mb_strlen(trim($login))==0){
            return false;
        }
        if (!$this->user_exists($login)){
            try {
                $this->ms->begin_transaction(name:"add_user");
                $stmt = $this->ms->prepare("INSERT INTO `users` (login, password, name) VALUES (?, ?, ?)");
                if ($stmt === false)
                    throw new \Exception("Ошибка подготовки запроса");
                if (!$stmt->bind_param("sss", $login, $password_hash, $name))
                    throw new \Exception("Ошибка связывания параметров");
                if (!$stmt->execute())
                    throw new \Exception("Ошибка выполнения запроса");
                $this->ms->commit(name:"add_user");
                return true;
            } catch (\Exception $e){
                $this->ms->rollback(name:"add_user");
                return false;
            }
        }
        return false;
    }

    public function user_exists(string $login): bool
    {
        if (!isset($login) || mb_strlen(trim($login))==0){
            return false;
        }
        $stmt = $this->ms->prepare("SELECT COUNT(login) FROM `users` WHERE `login`=?");
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_NUM);
        $res = $row[0];
        $result->close();
        $stmt->close();
        return $res > 0;
    }

    private function get_user_pass(string $user): string | null {
        $stmt = $this->ms->prepare("SELECT `password` FROM `users` WHERE `login`=?");
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $res = $row['password'];
        $result->close();
        $stmt->close();
        return $res;
    }

    public function auth_ok(string $user, string $pass): bool{
        if (!(mb_strlen($user) > 0 && mb_strlen($pass) > 0)) return false;
        if (!$this->user_exists($user)) return false;
        return password_verify($pass, $this->get_user_pass($user) ?? '');
    }
	
	public function get_products(string $param,int $select): array {
		$sort=$this->get_query_sort($select);
		$rows=array();
		if ($param=='') {
		$stmt = $this->ms->prepare("SELECT * FROM `products` " . $sort);
		}
		else {
			$param='%'.$param.'%';
			$stmt = $this->ms->prepare("SELECT * FROM `products` WHERE `products`.name LIKE ?" . $sort);
        $stmt->bind_param('s', $param);
		}
        $stmt->execute();
        $result = $stmt->get_result();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$rows[]=$row;
		}
        $result->close();
        $stmt->close();
        return $rows;
	}
	public function get_products_from_basket(int $user_id):array {
		$rows=array();
		$stmt = $this->ms->prepare("SELECT `name`, `product_id`, `order_id`, `amount_product`, `price`, `company`, `photo`, `description` FROM `products` INNER JOIN `basket` ON `products`.`id` = `product_id` WHERE `user_id` = ? AND `is_open` = 1");
		$stmt->bind_param('s', $user_id);
		$stmt->execute();
        $result = $stmt->get_result();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$rows[]=$row;
		}
        $result->close();
        $stmt->close();
        return $rows;
		
		
	}
	
	public function get_user_id(string $login): int {
		$stmt = $this->ms->prepare("SELECT id FROM `users` WHERE login=?");
		$stmt->bind_param('s', $login);
		$stmt->execute();
        $result = $stmt->get_result();
		$user_id_tmp =$result->fetch_array(MYSQLI_ASSOC);
		$user_id=$user_id_tmp['id'];
		$result->close();
        $stmt->close();
		return $user_id;
	}
	
	public function product_exists(int $product_id, int $amount): bool {
		$stmt = $this->ms->prepare("SELECT amount FROM `products` WHERE id=?");
		$stmt->bind_param('i', $product_id);
		$stmt->execute();
        $result = $stmt->get_result();
		$amount_tmp =$result->fetch_array(MYSQLI_ASSOC);
		$cur_amount=$amount_tmp['amount'];
		$result->close();
        $stmt->close();
		return $cur_amount >= $amount;
		
		
	}
	
	public function update_products(int $product_id, int $amount): bool {
		$rows=array();
		try {
                $this->ms->begin_transaction(name:"update_product");
                $stmt = $this->ms->prepare("UPDATE `products` SET amount=amount-? WHERE `products`.id=?");
                if ($stmt === false)
                    throw new \Exception("Ошибка подготовки запроса");
                if (!$stmt->bind_param("dd", $amount, $product_id))
                    throw new \Exception("Ошибка связывания параметров");
                if (!$stmt->execute())
                    throw new \Exception("Ошибка выполнения запроса");
                $this->ms->commit(name:"update_product");
                return true;
            } catch (\Exception $e){
                $this->ms->rollback(name:"update_product");
                return false;
            }
	}
	public function is_order_content_exists(int $user_id, int $order_id, int $product_id) : bool
    {
        $stmt = $this->ms->prepare("SELECT COUNT(`id`) FROM `basket` WHERE `user_id`=? AND `order_id` = ? AND `product_id` = ? AND `is_open`=1");
        $stmt->bind_param('iii', $user_id, $order_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_NUM);
        $res = $row[0];
        $result->close();
        $stmt->close();
        return $res > 0;
    }
	public function get_product_in_order_count(int $user_id, int $order_id, int $product_id) : int
    {
        $stmt = $this->ms->prepare("SELECT `amount_product` FROM `basket` WHERE `user_id` = ? AND `order_id` = ? AND `product_id` = ?");
        $stmt->bind_param("iii", $user_id, $order_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        return $row['amount_product'] ?? 0;
    }
	
	public function get_order_id(int $user_id) : int
    {
        $stmt = $this->ms->prepare("SELECT DISTINCT `order_id` FROM `basket` WHERE `user_id` = ? AND `is_open` = 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        return $row['order_id'] ?? 0;
    }
	
	public function get_next_order_id() : int
	{
        $stmt = $this->ms->prepare("SELECT MAX(`order_id`) AS max_order_id FROM `basket`");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        return $row['max_order_id'] ?? 0;
    }
	
	
		
	public function add_product(int $product_id,int $order_id, int $user_id,int $amount):bool{
		$rows=array();
		try {
			if (!$this->product_exists($product_id, $amount)) {
			throw new \Exception("Товара нет в наличии");
		}
		$this->ms->begin_transaction(name:"add_order");
		if($this->is_order_content_exists($user_id, $order_id, $product_id))
            {
                $stmt = $this->ms->prepare("UPDATE `basket` SET `amount_product`= ? WHERE `user_id`= ? AND `order_id` = ? AND `product_id` = ? AND `is_open`=1");
                $order_count = $this->get_product_in_order_count($user_id, $order_id, $product_id) + $amount;
                if ($stmt === false)
                    throw new \Exception("Ошибка подготовки запроса");
				
                if(!$stmt->bind_param("iiii", $order_count, $user_id, $order_id, $product_id))
                    throw new \Exception("Ошибка связывания параметров");
				
            }
            else
            {
                $stmt = $this->ms->prepare("INSERT INTO `basket` (user_id, order_id, product_id, amount_product) VALUES (?, ?, ?, ?)");
                if ($stmt === false)
                    throw new \Exception("Ошибка подготовки запроса");
                if (!$stmt->bind_param("iiii", $user_id, $order_id, $product_id, $amount))
                    throw new \Exception("Ошибка связывания параметров");
            }
            if (!$stmt->execute())
                throw new \Exception("Ошибка выполнения запроса");
            $this->ms->commit(name:"add_order");
            return true;
        } catch (\Exception $e){
            $this->ms->rollback(name:"add_order");
            return false;
        }
		
		
	}
	public function delete_products_from_basket(int $user_id, int $product_id):bool {
		try {
                $this->ms->begin_transaction(name:"delete_product");
                $stmt = $this->ms->prepare("DELETE FROM `basket` WHERE `basket`.user_id=? AND `basket`.product_id=?");
                if ($stmt === false)
                    throw new \Exception("Ошибка подготовки запроса");
                if (!$stmt->bind_param("dd", $user_id, $product_id))
                    throw new \Exception("Ошибка связывания параметров");
                if (!$stmt->execute())
                    throw new \Exception("Ошибка выполнения запроса");
                $this->ms->commit(name:"delete_product");
                return true;
            } catch (\Exception $e){
                $this->ms->rollback(name:"delete_product");
                return false;
            }
	}
	
	public function close_order(int $user_id, int $product_id):bool {
		try {
                $this->ms->begin_transaction(name:"close_order");
                $stmt = $this->ms->prepare("UPDATE `basket` SET `is_open`=0 WHERE `basket`.user_id=? AND `basket`.product_id=?");
                if ($stmt === false)
                    throw new \Exception("Ошибка подготовки запроса");
                if (!$stmt->bind_param("dd", $user_id, $product_id))
                    throw new \Exception("Ошибка связывания параметров");
                if (!$stmt->execute())
                    throw new \Exception("Ошибка выполнения запроса");
                $this->ms->commit(name:"close_order");
                return true;
            } catch (\Exception $e){
                $this->ms->rollback(name:"close_order");
                return false;
            }
	}
	
	private function get_query_sort(int $select):string {
		$sort='';
		switch($select) {
				case 1:
				$sort=" ORDER BY name";
				break;
				case 2:
				$sort=" ORDER BY price";
				break;
				case 3:
				$sort=" ORDER BY price DESC";
				break;
				case 4:
				$sort=" ORDER BY  name LIMIT 3";
				break;
			}
			return $sort;
		
	}
	public function get_filter_products(int $price1, int $price2,array $company, int $select):array {
		$sort=$this->get_query_sort($select);
		$stmt=null;
		if (count($company)==0) {
			$stmt = $this->ms->prepare("SELECT * FROM `products` WHERE `products`.price>=? AND `products`.price <=?" . $sort);
            $stmt->bind_param('ii', $price1,$price2);
		} else
			 {
				$checked='\''.(string) implode('\',\'', $company) . '\'';
				$parameters = str_repeat('?,', count($company) - 1) . '?'; 
				$str='ii' . str_repeat('s',count($company));
				$stmt = $this->ms->prepare("SELECT * FROM `products` WHERE `products`.price>=? AND `products`.price <=? AND `products`.company IN ($parameters)" . $sort);
                $stmt->bind_param($str, $price1,$price2, ...$company);
			}
		$rows=array();
        $stmt->execute();
        $result = $stmt->get_result();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$rows[]=$row;
		}
        $result->close();
        $stmt->close();
        return $rows;
	}
	
	//public function get_checked_products(array $checked):array {
	//	$rows=array();
	//	//print_r($checked);
	//	$checked=implode(',', $checked);
	//		$stmt = $this->ms->prepare("SELECT * FROM `products` WHERE `products`.company IN (?)");
     //   $stmt->bind_param('s', $checked);
      //  $stmt->execute();
       // $result = $stmt->get_result();
		//while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		//	$rows[]=$row;
		//}
        //$result->close();
        //$stmt->close();
        //return $rows;
	//}
	
	public function get_company():array {
		$rows=array();
			$stmt = $this->ms->prepare("SELECT DISTINCT company FROM `products`");
        $stmt->execute();
        $result = $stmt->get_result();
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$rows[]=$row;
		}
        $result->close();
        $stmt->close();
        return $rows;
	}

}
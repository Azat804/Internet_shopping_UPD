<?php

require_once ("common/page.php");
require_once ("common/a_content.php");
require_once ("common/db_helper.php");
require_once ("common/pagination.php");
class the_content extends \common\a_content {

    public function __construct(){
        $this->isProtected = false;
        parent::__construct();
        $this->check_user_data();
    }

    private function check_user_data(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['reset'])) {
				unset($_SESSION['checked_comp']);
	            unset($_SESSION['filter_price1']);
	            unset($_SESSION['filter_price2']);
	            unset($_SESSION['search']);
				unset($_SESSION['checked_comp_id']);
				unset($_SESSION['select']);
			}
			else {
            $company=$_SESSION['company'];
			$checked_comp=array();
			$checked_comp_id=array();
			for ($i=0; $i< count($company); $i++) {
				$name='c' . (string) $i;
				if (isset($_POST[$name]) && $_POST[$name]==$i) {
					$checked_comp_id[$i]=true;
					$checked_comp[]=$company[$i]['company'];
				}
			}
			$_SESSION['checked_comp_id']=$checked_comp_id;
			if (count($checked_comp)>0){
				$_SESSION['checked_comp']=$checked_comp;
			}
                if (isset($_POST['search'])) {
                    
                    $search = $_POST['search'];
                    
                        $_SESSION['search'] = htmlspecialchars($search);
                    
                }
				//else {
				//	unset($_SESSION['search']);
				//}
				if (isset($_POST['select'])) {
					$_SESSION['select']=$_POST['select'];
				}
				if (isset($_POST['filter_price1']) && isset($_POST['filter_price2'])) {
					if ($_POST['filter_price1']<= $_POST['filter_price2']) {
                    $filter_price1 = $_POST['filter_price1'];
                        $_SESSION['filter_price1'] = htmlspecialchars($filter_price1);
						$filter_price2 = $_POST['filter_price2'];
                        $_SESSION['filter_price2'] = htmlspecialchars($filter_price2);
					}
                    
                }
				//else {
				//	unset($_SESSION['filter_price1']);
				//	unset($_SESSION['filter_price2']);
				//}
			}
        }
    }
	
	private function print_pages($pages, $current_page):void {
		print('<ul class="pagination pagination-lg">');
			for($i=0; $i<count($pages); $i++) {
				
				if ($pages[$i][0]== $current_page ) {
					print('<li class="page-item active" aria-current="page">');
					print("<span class='page-link'>{$pages[$i][0]}</span>");
					print('</li>');
				}
				else {
					
				 print("<li class='page-item'><a class='page-link' href='{$pages[$i][1]}'>{$pages[$i][0]}</a></li>");
				}
				if ($i<count($pages)-2 && abs($pages[$i+1][0]-$pages[$i][0])>1) {
					
					if (abs($pages[$i+1][0]-$pages[$i][0])==2){
					print('<li class="page-item" aria-current="page">');
					$temp=$pages[$i][0]+1;
					print("<span class='page-link'>{$temp}</span>");
					print('</li>');	
					}
					else {
					print('<li class="page-item" aria-current="page">');
					print("<span class='page-link'>...</span>");
					print('</li>');
					}
				}
			}
			print('</ul>');
	}
	
    public function show_content(): void
    {
		$products=array();
		$select=(isset($_SESSION['select']))?$_SESSION['select']:1;
		if (isset($_SESSION['search'])) {
		$products=\common\db_helper::get_instance()->get_products($_SESSION['search'],$select);
	}
	else {
		$price1=0;
		$price2=10000;
		
		if (isset($_SESSION['filter_price1']) && isset($_SESSION['filter_price2']) && $_SESSION['filter_price1']!='' && $_SESSION['filter_price2']!='') {
			$price1=$_SESSION['filter_price1'];
		    $price2=$_SESSION['filter_price2'];
		}
		$comp= (isset($_SESSION['checked_comp']))?$_SESSION['checked_comp']:array();
		
		
		if ($price1==0 && $price2==10000 && count($comp)==0) {
        $products=\common\db_helper::get_instance()->get_products('',$select);
		}
		else {
		$products=\common\db_helper::get_instance()->get_filter_products($price1,$price2,$comp,$select);
		}
	}
	    try {
		if(count($products)>0) {
			
			$objects=$products;
			$current_page=1;
			$current_objects_per_page=7;
			if (isset($_GET['p']) && isset($_GET['c'])) {
				
				if (!in_array($_GET['c'],array(7,14,21))) {
					throw new ErrorException('Страница не найдена');
				}
				else {
					if (mb_eregi("^[0-9]+$", $_GET['p'])) { 
					
				$current_page=$_GET['p'];
				$current_objects_per_page=$_GET['c'];
			$p= new \common\pagination(count($products),$_GET['c'], $_GET['p']);
			$pages=$p->get_pages("product.php?p=");
			if ($_GET['p'] >$pages[count($pages)-1][0] || $_GET['p']==0){throw new ErrorException('Страница не найдена');}
			$interval=$p->get_objects_idx_by($_GET['p']);
					}
					else {throw new ErrorException('Страница не найдена');}
					
				}
			}
			else {
				$p= new \common\pagination(count($products), 7, 1);
				$pages=$p->get_pages("product.php?p=");
				$interval=$p->get_objects_idx_by(1);
			}
	
			print('<div class="dropdown text-start">');
			print('<span class="btn btn-outline-secondary dropdown-toggle my-2" role="button" data-bs-toggle="dropdown" aria-expanded="true">Кол-во товаров на странице</span>');
			print('<ul class="dropdown-menu">');
			$item7=explode("c=",$pages[0][1])[0]."c=7";
			$item14=explode("c=",$pages[0][1])[0]."c=14";
			$item21=explode("c=",$pages[0][1])[0]."c=21";
			switch($current_objects_per_page) {
				case 7:
				print("<li><span class='dropdown-item active'>7</span></li>");
				print("<li><a class='dropdown-item' href='{$item14}'>14</a></li>");
				print("<li><a class='dropdown-item' href='{$item21}'>21</a></li>");
				break;
				case 14:
				print("<li><a class='dropdown-item' href='{$item7}'>7</a></li>");
				print("<li><span class='dropdown-item active'>14</span></li>");
				print("<li><a class='dropdown-item' href='{$item21}'>21</a></li>");
				break;
				case 21:
				print("<li><a class='dropdown-item' href='{$item7}'>7</a></li>");
				print("<li><a class='dropdown-item' href='{$item14}'>14</a></li>");
				print("<li><span class='dropdown-item active'>21</span></li>");
				break;
			}
			
			print('</ul>');
			print('</div>');
		
	
		
		
		print('<div class="container-fluid text-start h-100 w-100  mx-0 px-0 g-0">');
		print('<div class="row w-100 mx-0">');
		print('<div class="col-8">');
		$this->print_pages($pages,$current_page);
		for($i=$interval[0]; $i<=$interval[1]; $i++) {
			?>
			
			
			<div class="card text-justify bg-body-secondary  g-0 my-2 pe-0 me-0">
			<div class="row g-0 ">
			<div class="col-12 col-md-4">
			<?php
			print("<img src='data/{$products[$i]['photo']}' class='img-fluid rounded-start p-1' alt='image1'>");
			?>
			</div>
    <div class="col-12 col-md-8">
      <div class="card-body">
		
		<h5 class="card-title text-center"><?php print $products[$i]['name']; ?></h5>
        <ul class="list-group list-group-horizontal w-100 text-start ">
  <li class="list-group-item flex-fill w-100 text-break">Описание</li>
  <li class="list-group-item flex-fill w-100"><?php print $products[$i]['description']; ?></li>
</ul>
<ul class="list-group list-group-horizontal w-100 text-start">
  <li class="list-group-item flex-fill w-100">Производитель</li>
  <li class="list-group-item flex-fill w-100"><?php print $products[$i]['company']; ?></li>
</ul>
<ul class="list-group list-group-horizontal w-100 text-start">
  <li class="list-group-item flex-fill w-100">Цена</li>
  <li class="list-group-item flex-fill w-100"><?php print $products[$i]['price']; ?> руб.</li>
</ul>
</div>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST[$products[$i]['id']])) {
	if (isset($_SESSION['user'])) {
		$order_id=0;
		$order=\common\db_helper::get_instance()->get_order_id($_SESSION['user_id']);
		if ($order!=0) {
			$order_id=$order;
		}
		else {
			$order_id=\common\db_helper::get_instance()->get_next_order_id() + 1;
		}
	$_SESSION['temp']=$products[$i]['id'];
	$add=\common\db_helper::get_instance()->add_product($products[$i]['id'], $order_id, $_SESSION['user_id'], $_POST[$products[$i]['id']]);
	//$_SESSION['add']=true;
	if (!$add) {
		print('<div class="alert alert-danger" role="alert">');
  print('Товара нет в наличии');
print('</div>');
	}
}
else {
	print('<div class="alert alert-danger" role="alert">');
  print('Для добавления в корзину необходимо авторизоваться');
print('</div>');
}
	}
	

?>
<form action=<?php print "product.php?p=$current_page" . "&c=$current_objects_per_page"; ?> method="post">
		<div class="input-group has-validation mb-3">
		<div class="col-auto">
    <label for=<?php print $products[$i]['id']; ?> class="col-form-label mx-3">Количество</label>
  </div>
  <div class="col-auto">
		<input type='number' name=<?php print $products[$i]['id']; ?> class='form-control w-75' placeholder='Укажите количество' value='1' min='1' required>
		  </div>
		  <div class="col-auto">
		<input type="submit" value="Добавить в корзину" class="btn btn-outline-secondary"  id="button-addon2">
		</div>
</div>
</form>
</div>
</div>
</div>
<?php
		}
		$this->print_pages($pages,$current_page);
		
		print('</div>');
		}
		else {
			?>
			'<div class="container-fluid text-start h-100 w-100  mx-0 px-0 g-0">
		<div class="row w-100 mx-0">
		<div class="col-8">'
		<div class="alert alert-danger text-center" role="alert">
  По вашему запросу ничего не найдено
</div>
</div>

		<?php
		}
	
?>

<div class="col-4 bg-primary bg-gradient bg-opacity-25  ">
<form action="product.php" method="post">
		<div class="input-group has-validation mb-3 mt-2">
		
    <label for="search" class="col-form-label me-2">Найти продукт </label>
  
  <div class="col-12 col-lg-auto"> 
		<input type='text' name='search' class='form-control ' placeholder='Введите название продукта' value=<?php print (isset($_SESSION['search']))?htmlspecialchars($_SESSION['search']):""; ?>>
		  </div>
		 <div class="col-auto"> 
		<input type="submit" value="Найти" class="btn btn-primary btn-opacity-50"  id="button-addon2" >
		</div>
</div>
</form>	
<form action="product.php" method="post">
		<div class="input-group has-validation mb-3">
		<div class="row w-100">
		<div class="col-auto">
    <label for='filter_price1' class="col-form-label me-1">Диапазон цены</label>
   </div>
  
		
		<input type='number' name='filter_price1' class='form-control  ms-2' placeholder='От' value=<?php print (isset($_SESSION['filter_price1']))?htmlspecialchars($_SESSION['filter_price1']):""; ?> min='0' max='9999'>
		
		
		<input type='number' name='filter_price2' class='form-control ms-2' placeholder='До' value=<?php print (isset($_SESSION['filter_price2']))?htmlspecialchars($_SESSION['filter_price2']):""; ?> min='0' max='9999'>

</div>
		 
		  
		<?php
		$company=\common\db_helper::get_instance()->get_company();
		if (!isset($_SESSION['company'])) {
		$_SESSION['company']=$company;
		}
		print('<div class="col-12">');
		print('<label for="flexCheckDefault" class="col-form-label">Производитель</label>');
		for ($i=0; $i< count($company); $i++) {
			print('<div class="form-check">');
			$name='c' . (string)$i;
			if (isset($_SESSION['checked_comp_id'][$i]) && $_SESSION['checked_comp_id'][$i]=true) {
		print("<input class='form-check-input' type='checkbox' name='{$name}' value='$i' id='flexCheckDefault' checked>");
			}
	        else {
		print("<input class='form-check-input' type='checkbox' name='{$name}' value='$i' id='flexCheckDefault'>");
			}
        print('<label class="form-check-label" for="flexCheckDefault">');
        print($company[$i]['company']);
  print('</label>');
 print('</div>');
  
		}
		print('</div>');
		
		
		?>
<div class="col-12">
<label for='select' class="col-form-label">Сортировать по</label>
<select class="form-select" name="select" aria-label="Default select example">
<?php
$selects=array('названию','возрастанию цены','убыванию цены');
for ($i=0;$i<count($selects);$i++) {
	$j=$i+1;
	if($_SESSION['select']==$j) {
		
		print("<option value='$j' selected>{$selects[$i]}</option>");
	}
	else {
		print("<option value='$j'>{$selects[$i]}</option>");
	}
} 
?>
</select>
</div>			
	<div class="col-auto mt-1">
		<input type="submit" value="Применить" class="btn btn-primary btn-opacity-50"  id="button-addon2" >
		</div>		
</div>
</form>
<form action="product.php" method="post">
  <input type="hidden" name='reset' value="3">
  <input type="submit" value="Сбросить" class="btn btn-primary btn-opacity-50 mb-2"  id="button-addon2" >
  </form>

</div>	

	</div>
	</div>
	<?php	
	}
	catch (ErrorException $e) {
	      print('<div class="alert alert-danger" role="alert">');
          print("<p class='text-center'>{$e->getMessage()}</p>");
          print('</div>');
		}	    
		
		
    }
}

$content = new the_content();
new \common\page($content);

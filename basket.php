<?php

require_once ("common/page.php");
require_once ("common/a_content.php");
require_once ("common/db_helper.php");
class the_content extends \common\a_content {

    public function __construct(){
        parent::__construct(); 
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
		$delete=\common\db_helper::get_instance()->delete_products_from_basket($_SESSION['user_id'], $_POST['delete']);
		}
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update']) && isset($_POST['amount'])) {
		$order_id=0;
		$order=\common\db_helper::get_instance()->get_order_id($_SESSION['user_id']);
		if ($order!=0) {
			$order_id=$order;
		}
		else {
			$order_id=\common\db_helper::get_instance()->get_next_order_id() + 1;
		}
	$add=\common\db_helper::get_instance()->add_product($_POST['update'], $order_id, $_SESSION['user_id'], $_POST['amount']);
			
		}
    }
	
    public function show_content(): void
    {	
		$flag=true;
		$products=\common\db_helper::get_instance()->get_products_from_basket($_SESSION['user_id']);
		$sum=0;
		for($i=0; $i<count($products); $i++) {
			?>
			<div class="card text-justify bg-body-secondary w-100 g-0 my-2 pe-0 me-0">
			<div class="row g-0 ">
			<div class="col-12 col-md-4">
			<?php
			print("<img src='data/{$products[$i]['photo']}' class='img-fluid rounded-start p-1' alt='image1' width='50%'>");
			?>
			</div>
    <div class="col-12 col-md-8">
      <div class="card-body">
		<h5 class="card-title"><?php print $products[$i]['name']; ?></h5>
        <ul class="list-group list-group-horizontal w-100 text-start ">
  <li class="list-group-item flex-fill w-100 text-break">Описание</li>
  <li class="list-group-item flex-fill w-100"><?php print $products[$i]['description']; ?></li>
</ul>
<ul class="list-group list-group-horizontal w-100 text-start">
  <li class="list-group-item flex-fill w-100">Производитель</li>
  <li class="list-group-item flex-fill w-100"><?php print $products[$i]['company']; ?></li>
</ul>
<ul class="list-group list-group-horizontal w-100 text-start">
  <li class="list-group-item flex-fill w-100">Количество</li>
  <li class="list-group-item flex-fill w-100"><?php print $products[$i]['amount_product']; ?></li>
</ul>
<ul class="list-group list-group-horizontal w-100 text-start">
  <li class="list-group-item flex-fill w-100">Стоимость</li>
  <li class="list-group-item flex-fill w-100"><?php print $products[$i]['price']*$products[$i]['amount_product']; ?> руб.</li>
</ul>
</div>
<form action="basket.php" method="post">
  <input type="hidden" name='delete' value=<?php print $products[$i]['product_id']; ?>>
  <input type="submit" value="Удалить из корзины" class="btn btn-outline-secondary my-2"  id="button-addon2" >
  </form>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy'])) {
	$flag=false;
	if (\common\db_helper::get_instance()->product_exists($products[$i]['product_id'],$products[$i]['amount_product'])) {
		print('<div class="alert alert-success" role="alert">');
  print('Товар куплен');
  print('</div>');
  $update=\common\db_helper::get_instance()->update_products($products[$i]['product_id'],$products[$i]['amount_product']);
	}
	else {
		$delete=\common\db_helper::get_instance()->delete_products_from_basket($_SESSION['user_id'], $products[$i]['product_id']);
		print('<div class="alert alert-danger" role="alert">');
  print('Товара нет в наличии');
  print('</div>');
	}
		$close=\common\db_helper::get_instance()->close_order($_SESSION['user_id'], $products[$i]['product_id']);
		
		}
$sum+=$products[$i]['price']*$products[$i]['amount_product'];
?>
<form action=<?php print "basket.php"; ?> method="post">
		<div class="input-group has-validation mb-3">
		<div class="col-auto">
    <label for=<?php print $products[$i]['product_id']; ?> class="col-form-label mx-3">Количество</label>
  </div>
  <div class="col-auto">
  <input type = 'hidden' name = 'update' value = <?php print $products[$i]['product_id']; ?> >
		<input type='number' name='amount' class='form-control w-75' placeholder='Укажите количество' value='1' min=<?php print ($products[$i]['amount_product']-1)*(-1); ?> required>
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
		if ($sum!=0 && $flag) {
		print('<div class="alert alert-info" role="alert">');
  print("<p>Ваш заказ № {$products[0]['order_id']}</p>");
  print("<p>Сумма к оплате: $sum руб.</p>");
  ?>
  <form action="basket.php" method="post">
  <input type="hidden" name="buy" value="1">
  <input type="submit" value="Оформить заказ" class="btn btn-primary btn-opacity-50 my-2 "  id="button-addon2" >
  </form>
  <?php
print('</div>');
		}
		else {
			print('<div class="alert alert-info" role="alert">');
  print('Корзина пуста');
  print('</div>');
		}	
    }
}

$content = new the_content();
new \common\page($content);
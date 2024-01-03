<?php
require_once ("common/page.php");
require_once ("common/a_content.php");
require_once ("common/db_helper.php");

class index extends \common\a_content {
	public function __construct()
    {
        $this->isProtected = false;
        parent::__construct();
    }
	
	private string $image = "data/image2.webp";
	
    public function show_content(): void {
		print("<img src='{$this->image}'   class='p-1' alt='image2' width='100%'>");
        print('<div class="alert alert-dark fs-4" role="alert">Товары дня</div>');	
    $products=\common\db_helper::get_instance()->get_products('',4);		
        for($i=0; $i<count($products); $i++) {
			?>
			
			
			<div class="card text-justify bg-body-secondary  g-0 my-2 pe-0 me-0">
			<div class="row g-0 ">
			<div class="col-12 col-md-4">
			<?php
			print("<img src='data/{$products[$i]['photo']}' class='img-fluid rounded-start p-1' alt='image1' width='50%'>");
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
?>
</div>
</div>
</div>
<?php
		}
    }
}

$content = new index();
new \common\page($content);
<?php
require_once ("common/page.php");
require_once ("common/a_content.php");

class Cart extends \common\a_content {
	
function show_catalog() {
	
}
	
    public function show_content(): void
    {
		
    }
}

$content = new Cart();
new \common\page($content);
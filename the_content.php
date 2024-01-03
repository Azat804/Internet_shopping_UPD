<?php
require_once ("common/page.php");
require_once ("common/a_content.php");
require_once ("common/simplexlsx.class.php");


class pagination {
    private $objects_count;
    private $objects_per_page;
	private $page_num;
	
    public function __construct(int $objects_count, int $objects_per_page, int $page_num) {
		
        $this->objects_count=$objects_count;
        $this->objects_per_page=$objects_per_page;
		$this->page_num=$page_num;

    }

    public function get_objects_idx_by(int $page_num):array | null {
        $start=min(($page_num-1)*$this->objects_per_page, $this->objects_count-1);
        $end=min($start+$this->objects_per_page, $this->objects_count)-1;
        if ($start<0 || $start>$end) {
            return null;
        }
        return array($start,$end);
    }

    private function get_page_count(): int{
        return intdiv($this->objects_count, $this->objects_per_page) +
            ($this->objects_count % $this->objects_per_page != 0);
    }
	

    public function get_pages(string $url_template): array{
        $max_pages = $this->get_page_count();
		$result=array();
        for ($i = 1; $i <= min(3, $max_pages); $i++){
            $url = $url_template."$i"."&c={$this->objects_per_page}";
            $result[] = array($i, $url);
        }
		if ($max_pages>3) {
		for ($i = min(max(1,$this->page_num-1),$max_pages-2); $i <= min($this->page_num+1, $max_pages); $i++){
            $url = $url_template."$i"."&c={$this->objects_per_page}";
			if (!in_array(array($i,$url), $result))
            $result[] = array($i, $url);
        }
		for ($i=max($max_pages-2,4); $i <= $max_pages; $i++) {
			$url = $url_template."$i"."&c={$this->objects_per_page}";
			if (!in_array(array($i,$url), $result))
            $result[] = array($i, $url);
		}
		}
        
		
		return $result;
    }

}
class the_content extends \common\a_content {
	
	public function __construct(){
        parent::__construct();
	}
	
	
    private string $user_files_dir = "user_files/";
	
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
	
	
    public function show_content(): void{
		
        ?>
<div class="border border-3 rounded-3 text-start p-2 my-2">Пользователь загружает через форму Excel файл, который состоит из 4 столбцов и около 10000 строк. Первый столбец содержит нумерацию строк, второй - формулу с ними. В третьем столбце содержится текст в кавычках, текст с точкой запятой, точка с запятой и текст с html-кодом. Необходимо принять файл от пользователя и отобразить на странице, разбивая ее на страницы.
 Также необходимо предоставить пользователю выбор количества объектов на одной странице(10, 50, 100).
 Следует проверить размер, расширение и формат файла перед отправкой на сервер.
		</div>
        <form action="the_content.php" method="post" enctype="multipart/form-data">
		<div class="input-group has-validation mb-3">
            <input type="hidden" name="MAX_FILE_SIZE" value="1024000">
			
			<?php
	
	
            print("<input type='file' name='userfile' class='form-control btn btn-outline-secondary'  id='formFile' accept='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/xls,text/xlsx' required>");
			?>
            <input type="submit" value="Отправить" class="btn btn-outline-secondary"  id="button-addon2">
			</div>
        </form>
		
        <?php
		try {
		$name = $this->get_file();
        if ($name !== null && $xlsx = SimpleXLSX::parse($name) ) {
			if(count($xlsx->rows())>0) {
			$objects=$xlsx->rows();
			$current_page=1;
			$current_objects_per_page=10;
			if (isset($_GET['p']) && isset($_GET['c'])) {
				
				if (!in_array($_GET['c'],array(10,50,100))) {
					throw new ErrorException('Страница не найдена');
				}
				else {
					if (mb_eregi("^[0-9]+$", $_GET['p'])) { 
					
				$current_page=$_GET['p'];
				$current_objects_per_page=$_GET['c'];
			$p= new pagination(count($objects),$_GET['c'], $_GET['p']);
			$pages=$p->get_pages("the_content.php?p=");
			if ($_GET['p'] >$pages[count($pages)-1][0] || $_GET['p']==0){throw new ErrorException('Страница не найдена');}
			$interval=$p->get_objects_idx_by($_GET['p']);
					}
					else {throw new ErrorException('Страница не найдена');}
					
				}
			}
			else {
				$p= new pagination(count($objects), 10, 1);
				$pages=$p->get_pages("the_content.php?p=");
				$interval=$p->get_objects_idx_by(1);
			}
	
			print('<div class="dropdown text-start">');
			print('<span class="btn btn-outline-secondary dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="true">Кол-во объектов на странице</span>');
			print('<ul class="dropdown-menu">');
			$item10=explode("c=",$pages[0][1])[0]."c=10";
			$item50=explode("c=",$pages[0][1])[0]."c=50";
			$item100=explode("c=",$pages[0][1])[0]."c=100";
			switch($current_objects_per_page) {
				case 10:
				print("<li><span class='dropdown-item active'>10</span></li>");
				print("<li><a class='dropdown-item' href='{$item50}'>50</a></li>");
				print("<li><a class='dropdown-item' href='{$item100}'>100</a></li>");
				break;
				case 50:
				print("<li><a class='dropdown-item' href='{$item10}'>10</a></li>");
				print("<li><span class='dropdown-item active'>50</span></li>");
				print("<li><a class='dropdown-item' href='{$item100}'>100</a></li>");
				break;
				case 100:
				print("<li><a class='dropdown-item' href='{$item10}'>10</a></li>");
				print("<li><a class='dropdown-item' href='{$item50}'>50</a></li>");
				print("<li><span class='dropdown-item active'>100</span></li>");
				break;
			}
			
			print('</ul>');
			print('</div>');
			
			$this->print_pages($pages,$current_page);
            echo '<table class="table table-bordered border-primary mx-0 mt-1">';
			
            for($j=$interval[0]; $j<=$interval[1]; $j++) {
				for($k=0; $k<count($objects[$j]); $k++){
					$objects[$j][$k]=htmlspecialchars($objects[$j][$k]);
				}
                echo '<tr><td style ="width: 25%;">'.implode('</td><td style ="width: 25%;">', $objects[$j] ).'</td></tr>';
            }
            echo '</table>';
			$this->print_pages($pages,$current_page);
			}
			else {
				unset($_SESSION['path']);
				throw new ErrorException('Неверный формат файла');
			}
            } else {
                echo SimpleXLSX::parse_error();
            }
		}
		catch (ErrorException $e) {
	      print('<div class="alert alert-danger" role="alert">');
          print("<p class='text-center'>{$e->getMessage()}</p>");
          print('</div>');	
}

			
    }

    private function get_file(): string | null
    {   
        if (!isset($_FILES['userfile']) && !isset($_SESSION['path'])) return null;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$name = $this->user_files_dir.pathinfo($_FILES['userfile']['tmp_name'],
		PATHINFO_FILENAME).".".pathinfo($_FILES['userfile']['name'],PATHINFO_EXTENSION);
		$_SESSION['path'] = $name;
		$_SESSION['type'] = $_FILES['userfile']['type'];
		$_SESSION['error'] = $_FILES['userfile']['error'];
		$extensions = array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-excel','text/xls','text/xlsx');
		if (!in_array($_SESSION['type'], $extensions) && $_SESSION['error']!=2) {
			unset($_SESSION['path']);
			throw new ErrorException('Недопустимый тип файла');
			}
		if ( $_SESSION['error']==2 ) {
			unset($_SESSION['path']);
			throw new ErrorException('Превышен допустимый размер файла');
			}
		if (@move_uploaded_file($_FILES['userfile']['tmp_name'], $_SESSION['path'] )) return $_SESSION['path'];
		}
		if (isset($_SESSION['path'])) return $_SESSION['path'];
        return null;
    }
}

$content = new the_content();
new \common\page($content);
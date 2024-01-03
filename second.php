<?php
require_once ("common/page.php");
require_once ("common/a_content.php");

class second extends \common\a_content {
	
	private function isPrime($number): bool
{
        if ($number==2)
                return true;
	if ($number%2==0 or $number==1)
		return false;
	$i=3;
	$max_factor = (int)sqrt($number);
	while ($i<=$max_factor){
		if ($number%$i == 0)
			return false;
		$i+=2;
	}
	return true;
}

private function getPrimes($max_number, $min_number): array
{
	$primes = [];
	for ($i=$min_number; $i<=$max_number; $i++){
		if ($this->isPrime($i))
			$primes[] = $i;
	}
	return $primes;
}
	
    public function show_content(): void
    {
		?>
		<div class="border border-3 rounded-3 text-start p-2 my-2">Пользователь задает два натуральных числа A и B. Необходимо вывести простые числа в интервале [A, B], записав их в таблицу с 10 столбцами. Если пользователь ничего не ввел, то таблица не строится.</div>
		<form action="second.php" method="post">
		<div class="input-group has-validation mb-3">
		<span class="input-group-text" id="inputGroup-sizing-default">A</span>
<input type="number" name="left" class="form-control" placeholder="Левая граница отрезка" required>
<span class="input-group-text" id="inputGroup-sizing-default">B</span>
<input type="number" name="right" class="form-control" placeholder="Правая граница отрезка" required>
<input type="submit" value="Задать отрезок" class="btn btn-outline-secondary"  id="button-addon2" >
</div>
</form>
<?php
if (isset($_POST['left']) && isset($_POST['right'])
	&& $_POST['left'] >=0 && $_POST['right']>=0 ) {
$cols = 10; 
$n=count($this->getPrimes($_POST['right'],$_POST['left']));
$k=0;
$rows = ceil($n /$cols);
print('<table class="table table-bordered border-primary mx-0 w-100">');
for ($tr=1; $tr<=$rows; $tr++){ 
    print('<tr>');
    for ($td=1; $td<=$cols; $td++){ 
									print('<td style ="width: 10%;">');
									if ($k < $n) {
        print("{$this->getPrimes($_POST['right'], $_POST['left'])[$k]}"); 
		$k++;
									}
									print('</td>');
    }
    print('</tr>');
}
print('</table>');
}
    }
}

$content = new second();
new \common\page($content);
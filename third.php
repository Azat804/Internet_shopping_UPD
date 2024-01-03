<?php
require_once ("common/page.php");
require_once ("common/a_content.php");

class third extends \common\a_content {
	

private function Narayana($digits): array
{   
	$j=0;
	$l=0;
	$k=0;
	for ($i=0; $i<count($digits)-1; $i++){
		if ($digits[$i]<$digits[$i+1])
			$j=$i;
	}
	for ($i=$j; $i<count($digits)-1; $i++) {
		if ($digits[$j]<$digits[$i+1])
			$l=$i+1;
	}
	[$digits[$j],$digits[$l]]=[$digits[$l],$digits[$j]];
	$reverse_digits=array_reverse(array_slice($digits,$j+1,count($digits)-$j-1));
	for ($i=$j+1; $i<count($digits); $i++) {
	$digits[$i]=$reverse_digits[$k];
	$k++;
	}
	return $digits;
}
	
    public function show_content(): void
    {
		?>
		<div class="border border-3 rounded-3 text-start p-2 my-2">Пользователь вводит ребус в виде математического выражения, содержащий буквы русского и английского алфавита без учета регистра, а также арифметические операции ("+", "-", "*", "/", "=").
		Необходимо закодировать буквы цифрами от 0 до 9, при этом одинаковым буквам соответсвуют одинаковые цифры. Вывести на экран введеный ребус и правильные математические выражения, которые соответвуют ребусу. Если различных букв больше 10 или введены недопустимые символы, или математическое выражение записано некорректно, то вывести сообщение об ошибке.</div>
		<form action="third.php" method="post">
		<div class="input-group has-validation mb-3">
		<?php
		if (isset($_POST['rebus'])) {
  print("<input type='text' name='rebus' class='form-control' placeholder='Ребус' value='{$_POST['rebus']}' required>");
		}
		else {
			print('<input type="text" name="rebus" class="form-control" placeholder="Ребус" required>');
		}

?>
<input type="submit" value="Ввести ребус" class="btn btn-outline-secondary"  id="button-addon2" >
</div>
</form>
<?php
$result=array();
if (isset($_POST['rebus'])) {
	$rebus=htmlspecialchars($_POST['rebus']);
	$text_lower=mb_strtolower($_POST['rebus']);
	$filtered_text = mb_strtolower(mb_eregi_replace("[^a-zа-я]", "", $_POST['rebus']));
        $arr = array_unique(mb_str_split($filtered_text));
		$value=array(0,1,2,3,4,5,6,7,8,9);
		$arr2= array_combine($value, array_pad(array_unique(mb_str_split($filtered_text)),10,''));
if ( mb_eregi("^-?[a-zа-я]+(?:[+*\/-][a-zа-я]+)*=-?[a-zа-я]+(?:[+*\/-][a-zа-я]+)*", $_POST['rebus'])
	&& !mb_eregi("[^a-zа-я+\/*=-]", $_POST['rebus']) && substr_count($_POST['rebus'],"=")==1 && count($arr)<=10) {
			$value=array(0,1,2,3,4,5,6,7,8,9);
			$n=3628800;
			for ($i=0; $i<$n; $i++){
				$text=$text_lower;
			$alph=array_combine($arr,array_slice($value,0,count($arr)));
			foreach ($alph as $val) {
				$word=array_keys($alph,$val)[0];
				$text = str_replace($word,$val,$text);
			
			}
			$txt=eval("return 0123+245==675;");
			// \b(?:0+)([1-9]\d*) $1 - замена
			if (!mb_ereg("(?:^0[0-9]|[+\/*=-]0[0-9]|\/0)", $text)) { 
			$math = explode("=", $text);
			$math_res=$math[0] . "==" . $math[1];
			 $s=eval('return ' . $math_res . ';');
			if ($s==1){
			$o=array_push($result,$text);
			}
			}
			$value=$this->Narayana($value);
			}
			$result=array_unique($result);
print('<table class="table table-bordered border-primary mx-0 w-">');
print('<tr>');
print("<td class='fw-bold'>$rebus</td>");
print('</tr>');
foreach ($result as $res) {
	print("<tr><td>$res</td></tr>");
}
print('</table>');
}

else {
	print("<p class='fw-bold text-center'>$rebus</p>");
	print('<p class="text-danger text-center">Ошибка</p>');
}
	}
    }
}

$content = new third();
new \common\page($content);
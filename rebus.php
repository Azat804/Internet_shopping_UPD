<?php

require_once ("common/page.php");
require_once ("common/a_content.php");

class rebus {
    private array $perm; 
    private string $rebus_str;
    public function __construct(string $str)
    {
        $this->perm=range(0,9);
        $this->rebus_str=mb_strtoupper(str_replace("=","==",$str));

    }
    private function nextPermutation(): bool{
        $i= count($this->perm)-2;
        while ($i>=0 && $this->perm[$i] >= $this->perm[$i+1]){
            $i--;
        }
        if($i<0) return false;
        $j=count($this->perm)-1;
        while ( $this->perm[$j]<=$this->perm[$i]){
            $j--;
        }
        $this->swap($i,$j);

        $this->reverse($i+1);
        return true;

    }
    private function reverse ($from_index){
        $l=$from_index;
        $r=count($this->perm)-1;
        while ($l<$r){
            $this->swap($l,$r);
            $l++;
            $r--;
        }
    }
    private function swap($i,$j){
        $temp=$this->perm[$i];
        $this->perm[$i]=$this->perm[$j];
        $this->perm[$j]=$temp;
    }
    public function solve(): array{
        //Шаг1. Соответствие формату
        $flag1=mb_ereg_match("^[+-]?[A-ZА-Я]+(?:[-+*\/][A-ZА-Я]+)*==[+-]?[A-ZА-Я]+(?:[-+*\/][A-ZА-Я]+)*$",$this->rebus_str);
        if (!$flag1){
            throw new ErrorException('Строка имеет неверный формат');
        }
        //Шаг2. Проверка на допустимое количество уникальных букв

        $alphabet=array_unique( mb_str_split( mb_ereg_replace("[^A-ZА-Я]", "", $this->rebus_str)));
        $n=count($alphabet);
        if ($n>10||$n<1){
            throw new ErrorException('В строке слишком много различных букв');
        }
        //Шаг3. Решение Ребуса
        $result=array();
        $dict=array();
        do  {
            $old_dict=$dict;
            $dict=array_combine($alphabet,array_slice($this->perm,0,$n));
            if($old_dict===$dict) continue;
            $rebus_digits=strtr($this->rebus_str,$dict);
            $rebus_digits=mb_ereg_replace('\b(?:0+)([1-9]\d*)','\1',$rebus_digits);
			
			if (mb_ereg("\/0",$rebus_digits)) {continue;}
            if(eval("return $rebus_digits;")){
                 $result[]=str_replace("==","=",$rebus_digits);
            }

        } while ($this->nextPermutation());
        return $result;

    }
}

class rebus_page extends \common\a_content {
    public function show_content(): void{
		?>
		<div class="border border-3 rounded-3 text-start p-2 my-2">Пользователь вводит ребус в виде математического выражения, содержащий буквы русского и английского алфавита без учета регистра, а также арифметические операции ("+", "-", "*", "/", "=").
		Необходимо закодировать буквы цифрами от 0 до 9, при этом одинаковым буквам соответсвуют одинаковые цифры. Вывести на экран введеный ребус и правильные математические выражения, которые соответвуют ребусу. Если различных букв больше 10 или введены недопустимые символы, или математическое выражение записано некорректно, то вывести сообщение об ошибке.</div>
		<form action="rebus.php" method="post">
		<div class="input-group has-validation mb-3">
		<?php
		$rebus_input='';
		if (isset($_POST['rebus'])) {
			$rebus_input=htmlspecialchars($_POST['rebus']);
  print("<input type='text' name='rebus' class='form-control' placeholder='Ребус' value='{$rebus_input}' required>");
  
		}
		else {
			print('<input type="text" name="rebus" class="form-control" placeholder="Ребус" required>');
		}

?>
<input type="submit" value="Ввести ребус" class="btn btn-outline-secondary"  id="button-addon2" >
</div>
</form>
<?php
if (isset($_POST['rebus'])) {
        $r= new rebus($rebus_input);
		try {
        $result = $r->solve();
		if (count($result)==0) {
			print('<div class="alert alert-warning" role="alert">');
			print("<p class='fw-bold'>$rebus_input</p>");
			print('<p>Нет решений</p>');
			print('</div>');	
		}
		else {
		print('<table class="table table-bordered border-primary mx-0 w-100">');
print('<tr>');
print("<td class='fw-bold'>$rebus_input</td>");
print('</tr>');
foreach ($result as $res) {
	print("<tr><td>$res</td></tr>");
}
print('</table>');
		}
}
catch (ErrorException $e) {
	print('<div class="alert alert-danger" role="alert">');
	print("<p class='fw-bold'>$rebus_input</p>");
  print("<p>{$e->getMessage()}</p>");
print('</div>');	
}
}
    }
}

$content = new rebus_page();
new \common\page($content);
<?php

namespace common;
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
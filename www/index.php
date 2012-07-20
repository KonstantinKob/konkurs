<?php 
/**
* We establish the charset and level of errors
* Устанавливаем кодировку и уровень ошибок
*/
	header("Content-Type: text/html; charset=utf-8");
	error_reporting(E_ALL);
/**
* Debug
* Дебаггер
* @TODO To clean in release
*/
   define('IRB_TRACE', true);
   include './debug.php';
/**
* We include buffering 
* Включаем буфферизацию 
*/
	ob_start();
    
        
    function Allocation_Word($input, $sheet, $tag = 'span' , $color = 'red', $style = '"background:')
    {
	$array_word = array();
	
	if(is_array($input))
	{
	    foreach($input as $val )
	    
		if(!$val == '')
		    $array_word[] = preg_quote($val);
	    
	}
	else
	{
	    // разделяем ключевые слова и записываем их в массив
            $array_temp = explode(',', $input);
	    
	    foreach($array_temp as $val)
		$array_word = array_merge($array_word, explode(' ', $val));
		
	    $array_temp = array_map('trim', array_unique($array_word));
	    $array_word = array();
	    
	    foreach($array_temp as $val)
	    {
	    	if(!$val == '')
		   $array_word[] = preg_quote($val);
	    }
	}
	
        if(count($array_word) > 0 && !$sheet == '')
	{
            $array_tags = array();
	    $array_color = array('aqua', 'black', 'blue', 'fuchsia', 'gray', 'green',
				 'lime', 'maroon', 'navy', 'olive', 'purple', 'red',
				 'silver', 'teal', 'white', 'yellow'
				);
	    
	    // определяем тип выделения
	    if(strtolower($tag) == 'span')
	    {
		// определяем цвет выделения
	        $color = strtolower($color);
	        if(!in_array($color, $array_color))
		    $color = 'red';
		    
		$begin_tag = '<span ' . $tag . ' style=' . $style . $color . '">';
		$end_tag = '</' . $tag . '>';
	    }
	    else
	    {
		if($style == '"background:')
		    $style = '';
		else
		    $style = ' style=' . $style;
		    
		$begin_tag = '<span><' . $tag . $style . '>';
		$end_tag = '</' . $tag . '></span>';
	    }
	    
	    //убираем теги со страницы
            $patern = '#<(.*)>#iuU';
	    $kol_tags = preg_match_all($patern, $sheet, $array_tags);
	    $first_divided = preg_split($patern, $sheet);
	   
	    $sheet = $first_divided[0];
	    
	    for($i = 1; $i < count($first_divided); $i++)// перебираем кусочки без тегов
	    {
		$part_sheet = $first_divided[$i];
		foreach($array_word as $val)
		    $part_sheet = preg_replace('#' . $val . '#iu', $begin_tag . '\\0' . $end_tag, $part_sheet);
		    
		$sheet .= $array_tags[0][$i - 1] . $part_sheet;// собирем страницу обратно с тегами.
	    }
	}
	
	return $sheet;
    
    }
    

    $ok = !empty($_POST['ok'])?true:false;
    
    if($ok)
    {
	include './forma.tpl';
        $sheet = file_get_contents('./first.tpl');
	
        $a = Allocation_Word($_POST['word'], $sheet, 'span', 'lime');
	echo $a;
    }
    else
    {
        include './forma.tpl';
        include './first.tpl';
    }
    
    $content = ob_get_contents();
    ob_end_clean();
    
    include './index.tpl';
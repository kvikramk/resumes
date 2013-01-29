<?php
$path="resumes/";
if (is_dir($path)) {
    if ($dh = opendir($path)) {
        while (($file = readdir($dh)) !== false) {
            process_file($path.$file);
        }
        closedir($dh);
    }
}

function process_file($filename){

    $fileContent = shell_exec('antiword '.$filename." -f");
    var_dump($fileContent);
    $arr = explode("\n", $fileContent);
    $LINENUMBERLIMIT=20; //Number of lines to be processed to fetch name, email and phone number;
    $temp=trimnew_alines($arr);
    $relevantText=implode(" ",$temp);
    $emails=extract_emails($relevantText);
    foreach($emails as $email){
        echo "Email of the user is ".$email."<br/>";
    }
    $phones=getphone_numbers($relevantText);
    foreach($phones as $no){
        echo "Phone Number of the user is ".$no."<br/>";
    }
    $boldWords=gethighlighted_words($relevantText);
    var_dump($boldWords);
    fetch_names($boldWords,$emails);
    //fetch_names($temp,$emails);   
}
function fetch_names($boldWords,$email){
    $tempname="";
    foreach($boldWords as $word){
        var_dump($word);
        $wordsarray = explode(" ", $word);
        foreach($wordsarray as $keys){
            if(stripos($email[0],$keys)!==FALSE){
                $tempname.=$keys." ";
            }
        }
        $tempname=substr($tempname,0,-1);
    }
    if(strlen($tempname)!==0){
        echo "Name of the User is $tempname <br/>";
    }
}

function trimnew_lines($textarray){ // "\n" from a array
    global $LINENUMBERLIMIT;
    $i=0;
    $returnArray=array();
    foreach($textarray as $text){
        if($i===$LINENUMBERLIMIT){
          break;
        }
        if($text===''){
            continue;
        }else{
            array_push($returnArray,$text);
            $i++;
        }
    }
    return $returnArray;
}

function getconsecutive_capitalwords($str){
    preg_match_all("/([A-Z][\w-]*(\s+[A-Z][\w-]*)+)/", $strin, $matches);
    return $matches[1]; 
}

function gethighlighted_words($strin){
    preg_match_all("/\*(\w+\s\w+)\*/", $strin, $matches); //extract bolded words
    return $matches[1];
}

function getphone_numbers($string){
    preg_match_all("/(\d{10})/", $string, $matches); //extract bolded words
    return $matches[0];
}

function extract_emails($string){
    preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
    return $matches[0];
}
//var_dump($arr);
//var_dump($matches[0]);
//var_dump($fileContent);
?>
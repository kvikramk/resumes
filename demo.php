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
   // var_dump($fileContent);
    $arr = explode("\n", $fileContent);
    $LINENUMBERLIMIT=20; //Number of lines to be processed to fetch name, email and phone number;
    $temp=trimnew_lines($arr);
    $relevantText=implode(" ",$temp);
    $emails=extract_emails($relevantText);
    foreach($emails as $email){
        echo "Email of the user is ".$email."<br/>";
    }
    $phones=getphone_numbers($relevantText);
    foreach($phones as $no){
        echo "Phone Number of the user is ".$no."<br/>";
    }
    $boldWords=getconsecutive_capitalwords($relevantText);
    fetch_names($boldWords,$emails[0]);
    echo "<br/><br/>";
    //fetch_names($temp,$emails);   
}
function fetch_names($wordsarray,$reference){
    $tempname="";
    foreach($wordsarray as $word){
        $wordsarray = explode(" ", $word);
        foreach($wordsarray as $keys){
            if(stripos($reference,$keys)!==FALSE){
                $tempname.=$keys." ";
            }

        }
        if($tempname!==""){
            $tempname=substr($tempname,0,-1);
            break;    
        }
        
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

function getconsecutive_capitalwords($strin){
    preg_match_all("/([A-Z][\w-]*(\s+[A-Z][\w-]*)+)/", $strin, $matches);
    return $matches[1]; 
}

function gethighlighted_words($strin){
    preg_match_all("/\*([^\*]*?)\*/", $strin, $matches); //extract bolded words
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
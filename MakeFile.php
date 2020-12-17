<?php
function getDIR(){
    $file_path = realpath(__FILE__);
    $real_path = str_replace(basename(__FILE__), '', $file_path); //basename: 파일만 기져옴
    return $real_path;
}

function Initialize(){
    global $argc, $argv;
    if($argc < 4){
        $argv[1] = 'cpp';
        if($argc < 3) $argv[2] = '';
        if($argv < 4) $argv[3] = '';
    }
}

function write_mackefile($WF){
    fprintf($WF, "$(OUTPUT): $(OBJECT)\n");
    fprintf($WF, "\x09$(CC) $(FLAGS) -o $@ $^\n\n");
    fprintf($WF, "clean:\n");
    fprintf($WF, "\x09rm *.o");
}

Initialize();

$MF = fopen('Makefile', 'w'); # Makefile 파일 생성

$Object_file = Array();

$Makefile_array = array( //MakeFile에 필수로 들어가야하는 매크로 정의 
    'CC' => ($argv[1]=='cpp') ? 'g++' : 'gcc',
    'FLAGS' => '-std='.(($argv[2]=='20') ? 'c++20' : 'c++1z').' -Wall',
    'OUTPUT' => ($argv[3]=='') ? 'start' : $argv[3],
    'OBJECT' => ''
);

if($MF){
    $cur_dir = getDIR(); # 파일이 있는 디렉토리 가져오기

    ################## 디렉토리 핸들 선정 ###################
    $handle = opendir($cur_dir); 
    ####################################################

    ################## 파일 모음집(?)   ###################
    $dir_files = Array();
    ####################################################

    while(false !== ($file = readdir($handle))){
        if($file == 'Makefile' || $file==basename(__FILE__)) continue;
        if(is_file($cur_dir.$file)){ //파일인지 체크후(디렉토리 제외)
            $dir_files[] = $file; //맞다면 dir_files에 추가
        }
    }
    closedir($handle); //핸들 삭제

    sort($dir_files); //정렬

    foreach($dir_files as $files) array_push($Object_file, str_replace(($argv[1]=="cpp") ? '.cc' : '.c', '.o', $files));
    foreach($Makefile_array as $MACRO_NAME => $MACRO) if($MACRO_NAME != 'OBJECT') fprintf($MF, "%s = %s\n", $MACRO_NAME, $MACRO);
    foreach($Object_file as $o) $Makefile_array['OBJECT'] = $Makefile_array['OBJECT'].' '.$o;
    fprintf($MF, "OBJECT = %s\n", $Makefile_array['OBJECT']);

    write_mackefile($MF);
}
fclose( $MF );
?>
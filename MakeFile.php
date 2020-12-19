<?php
function getDIR(){
    $file_path = realpath(__FILE__);
    $real_path = str_replace(basename(__FILE__), '', $file_path); //basename: 파일만 기져옴
    return $real_path;
}

function Initialize(){
    global $argc, $argv;
    if($argc < 3){
#       $argv[0] = 'cpp';
        if($argc < 2) $argv[1] = '';
        if($argv < 3) $argv[2] = '';
    }
}

function write_mackefile($WF){
    global $CC_CHK, $C_CHK;
    fprintf($WF, "$(OUTPUT): $(%s)\n", ($CC_CHK ? 'CCOBJECT' : 
                                        ($C_CHK ? 'COBJECT' : '')));
    fprintf($WF, "\x09$(CC) $(FLAGS) -o $@ $^\n\n");
    fprintf($WF, "clean:\n");
    fprintf($WF, "\x09rm *.o");
}

Initialize();

$MF = fopen('Makefile', 'w'); # Makefile 파일 생성

$C_CHK = $CC_CHK = false;

function chk_compiler(&$arr, $chk1, $chk2){
    if($chk1) $arr['C'] = 'gcc';
    if($chk2) $arr['CC'] = 'g++';
}

$Object_file_C = Array();
$Object_file_CC = Array();

$Makefile_array = array( //MakeFile에 필수로 들어가야하는 매크로 정의 
#   'CC' => ($argv[1]=='cpp') ? 'g++' : 'gcc',
    'C' => '',
    'CC' => '',
    'FLAGS' => '-std='.(($argv[1]=='20') ? 'c++20' : 'c++1z').' -Wall',
    'OUTPUT' => ($argv[2]=='') ? 'start' : $argv[2],
    'COBJECT' => '',
    'CCOBJECT' => '',
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
            $tmp = explode(".", $file); //확장자 분리
            $dir_files[$file] = $tmp[1]; //맞다면 $file을 키로 가지는 확장자를 넣는다
        }
#        printf("%s\n", $dir_files["test.cc"]);
    }
    closedir($handle); //핸들 삭제

#    sort($dir_files); //정렬

    foreach($dir_files as $files => $file_ext) {
#        array_push(($file_ext=="c") ? $Object_file_C : $Object_file_CC, str_replace('.'.$file_ext, '.o', $files));
#        printf("%s", $files);
        if($file_ext == "c") {
            array_push($Object_file_C, str_replace('.'.$file_ext, '.o', $files));
#            printf("%s\n", $Object_file_C[0]);
            $C_CHK = true;
        }
        else if($file_ext == "cpp" || $file_ext=="cc"){ //나머지는 C++로 취급
            array_push($Object_file_CC, str_replace('.'.$file_ext, '.o', $files));
#            printf("%s\n", $Object_file_CC[0]);
            $CC_CHK = true;
        }
        #array_push($Object_file, str_replace(($argv[1]=="cpp") ? '.cc' : '.c', '.o', $files));
    }

    chk_compiler($Makefile_array, $C_CHK, $CC_CHK);

    if($C_CHK) foreach($Object_file_C as $o) $Makefile_array['COBJECT'] = $Makefile_array['COBJECT'].' '.$o;    
    if($CC_CHK) foreach($Object_file_CC as $o) $Makefile_array['CCOBJECT'] = $Makefile_array['CCOBJECT'].''.$o;

    printf("결과: %s\n", $Makefile_array['CC']);

    foreach($Makefile_array as $MACRO_NAME => $MACRO) if($MACRO != '') {
        fprintf($MF, "%s = %s\n", $MACRO_NAME, $MACRO);
    }

#   fprintf($MF, "CCOBJECT = %s\n", $Makefile_array['CCOBJECT']);

    write_mackefile($MF);
}
fclose( $MF );
?>
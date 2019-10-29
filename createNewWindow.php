<?php
function newWindow($path){
    $htmldir = '';
    $htmlfile = '';
    $files = glob($path.'\*');
    foreach($files as $filename){
        $nameFile = array_reverse (explode("\\",$filename))[0];
        if (is_dir( $filename))
        {
            $htmldir .= '<div class="directory">';
            if(1) //здесь будет функция проверки прав доступа у конкретной директории
            {
                $htmldir .= '<div class="hide">
                                <input class="changeMod" type="button" value="&nbsp;">
                                <input class="download" type="button" value="&nbsp;">
                                <input class="delete" type="button" value="&nbsp;" onclick="deleteDirectory(\''.$nameFile.'\')">
                             </div>
                            <img src="dir.png" onclick="changeDirectory(\''.$nameFile.'\')" />'.$nameFile.'
                        </div>';
            }
            else
            {
                $htmldir .='<img src="dir.png" />'.$nameFile.'
                        </div>';
            }
        }
        else 
        {
            $htmlfile .= '<div class="file">';
            if(1) //здесь будет функция проверки прав доступа у конкретного файла
            {
                $htmlfile .= '<div class="hide">
                                <input class="changeMod" type="button" value="&nbsp;">
                                <input class="download" type="button" value="&nbsp;">
                                <input class="delete" type="button" value="&nbsp;">
                              </div>
                            <img src="file.png" />'.$nameFile.'
                        </div>';
            }
            else 
            {
                $htmlfile .= '<img src="file.png" />'.$nameFile.'
                        </div>';
            }
        }
    }
    return $htmldir.$htmlfile;
}
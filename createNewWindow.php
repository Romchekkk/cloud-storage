<?php
function newWindow($path){
    $htmldir = "";
    $htmlfile = "";
    $files = glob($path."\*");
    foreach($files as $filename){
        $nameFile = array_reverse (explode("\\",$filename))[0];
        if (is_dir($filename))
        {
            $htmldir .= "<div class=\"directory\">";
            if(1) //здесь будет функция проверки прав доступа у конкретной директории
            {
                $htmldir .= "<div class=\"hide\">
                                <input class=\"changeMod\" type=\"button\" value=\"&nbsp;\" />
                                <input class=\"delete\" type=\"button\" value=\"&nbsp;\" onclick=\"deleteDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                             </div>
                            <img src=\"images/dir.png\" onclick=\"changeDirectory('".preg_replace("/'/uis", "\'", $nameFile)."')\" />$nameFile
                        </div>";
            }
            else
            {
                $htmldir .= "<img src=\"images/dir.png\" />$nameFile
                        </div>";
            }
        }
        else 
        {
            $htmlfile .= "<div class=\"file\">";
            if(1) //здесь будет функция проверки прав доступа у конкретного файла
            {
                $htmlfile .= "<div class=\"hide\">
                                <input class=\"changeMod\" type=\"button\" value=\"&nbsp;\" />
                                <input class=\"download\" type=\"button\" value=\"&nbsp;\" onclick=\"downloadFile('$path','".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                                <input class=\"delete\" type=\"button\" value=\"&nbsp;\" onclick=\"deleteFile('".preg_replace("/'/uis", "\'", $nameFile)."')\" />
                              </div>
                            <img src=\"images/file.png\" />$nameFile
                        </div>";
            }
            else 
            {
                $htmlfile .= "<img src=\"images/file.png\" />$nameFile
                        </div>";
            }
        }
    }
    return $htmldir.$htmlfile;
}
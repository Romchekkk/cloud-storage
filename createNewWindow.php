<?php
function newWindow($path){
    $htmldir = '';
    $htmlfile = '';
    $files = glob($path.'\*');
    foreach($files as $filename){
        $nameFile = array_reverse (explode("\\",$filename));
        if (is_dir( $filename))
        {
            $htmldir .= '<div class="directory">';
            if(1) //здесь будет функция проверки прав доступа у конкретной директории
            {
                $htmldir .= '<div class="hide">
                                <input class="changeMod" type="button" value="&nbsp;">
                                <input class="download" type="button" value="&nbsp;">
                                <input class="delete" type="button" value="&nbsp;">
                             </div>
                            <img src="dir.png" />'.$nameFile[0].'
                        </div>';
            }
            else
            {
                $htmldir .='<img src="dir.png" />'.$nameFile[0].'
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
                            <img src="file.png" />'.$nameFile[0].'
                        </div>';
            }
            else 
            {
                $htmlfile .= '<img src="file.png" />'.$nameFile[0].'
                        </div>';
            }
        }
    }
    print $htmldir.$htmlfile;
}
<?php
header('Content-Type: text/plain');
if(defined(RESPONSE))
{
    switch(RESPONSE)
    {
        case 200: $str = 'OK'; break;
        case 400: $str = 'Bad Request'; break;
        case 404: $str = 'Not Found'; break;

        default: $str = 'Unknown/Bad Header'; 
    }

    header(sprintf('HTTP/1.1 %d %s', RESPONSE, $str));
}
echo json_encode($json, JSON_PRETTY_PRINT);

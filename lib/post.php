<?php
class post extends Controller
{
    const ALLOWED_HTML = 'b|i|em|strong|dfn|code|samp|kbd|var|cite|del|ins|strike|s|u|ol|ul|li|dt|dd|dl|sup|sub|small|big|image|link|code';

    public function __construct()
    {
        !BUNZ_BUNZILLA_ALLOW_ANONYMOUS && $this->requireLogin();
        if(selectCount('reports','ip = '.db()->quote(remoteAddr()).' AND time >= UNIX_TIMESTAMP() - 30'))
            $this->abort('stop spamming D:');
        parent::__construct();
    }

    public function index()
    {
        $this->tpl .= '/index';
        $this->data = ['categories' => db()->query('SELECT id,title,caption,color,icon FROM categories ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function category($id)
    {
        $this->tpl .= '/category';
        $result = db()->query(
            'SELECT id, title, description, reproduce, expected, actual
             FROM categories 
             WHERE id = '.(int)$id
        );
        if(!$result->rowCount())
            $this->abort('No such category!');

        $this->data['category'] = $result->fetch(PDO::FETCH_ASSOC);
        $this->data['params'] = [
            'email' => filterOptions(1,'email'),
            'subject' => filterOptions(0,'full_special_chars'),
            'status' => filterOptions(0,'number_int')
        ];
        foreach(['description','reproduce','expected','actual'] as $field)
        {
            if($this->data['category'][$field])
                $this->data['params'][$field] = filterOptions(0,'callback',null,[$this,'messageFilter']);
        }

        $this->data['params'] = filter_input_array(INPUT_POST,$this->data['params']);

        if(!empty($_POST))
            $this->createReport();
    }

    private function messageFilter($msg)
    {
// $message_body = nl2br (trim (str_replace (array (chr (7),chr (160),chr (173)), '', preg_replace ('@[ ]{2,}@', ' ', preg_replace ('@[\n]{2,}@', "\n", $message_body)))));
        $msg = htmlspecialchars($msg);
        $msg = trim(str_replace([chr(7),chr(160),chr(173)], '', $msg));
        $msg = preg_replace('/&lt;(\/)?('.self::ALLOWED_HTML.')&gt;/i', '<$1$2>', $msg);


        preg_match_all('/\<image\>(.{1,})\<\/image\>/i', $msg, $images, PREG_SET_ORDER);
        foreach($images as $image)
        {
            if(preg_match('/^http(s)?:\/\//', $image[1]))// && @getimagesize($image[1]))
                $msg = str_replace($image[0],'<img src="'.$img[1].'" alt="Image could not be loaded.">',$msg);
            else
                $msg = str_replace($image[0],'&lt;!-- Invalid Image URL! --&gt;',$msg);
        }

        preg_match_all('/\<link\>(.+?)(\{(.*?)\})*\<\/link\>/i',$msg, $links, PREG_SET_ORDER);
        foreach($links as $link)
        {
            $location = preg_match('/^http(s)?:\/\//', $link[1]) ? $link[1] : 'about:blank';
            $title = isset($link[3]) && strlen(trim($link[3])) ? trim($link[3]) : $location;
            $title = strlen($title) > 70 ? substr($title,0,35).' ... '.substr($title,-15) : $title;
            $msg = str_replace($link[0], '<a href="'.$location.'" title="'.$location.'" target="_blank">'.$title.'</a>',$msg);
        }

        $msg = nl2br($msg);

        preg_match_all('/\<code(\s+lang="[a-z0-9]+")?\>(.+?)\<\/code\>/ims', $msg, $codes, PREG_SET_ORDER);
        foreach($codes as $code)
        {
            $code[2] = str_replace('<br />', '', $code[2]);
            $msg = str_replace($code[0],'<pre><code'.$code[1].'>'.$code[2].'</code></pre>',$msg);
        }

        return $msg;
    }

    private function createReport()
    {
        foreach($this->data['params'] as $field => $value)
        {
            if(empty($value))
            {   $this->flash[] = $field .' cannot be blank.';
                continue;
            }
            switch($field)
            {
                case 'subject':
                    $this->data['params'][$field] = $value = trim(str_replace([chr(7),chr(160),chr(173),"\n","\r","\t"], '', preg_replace('/\s{2,}/',' ',$value)));
                    if(selectCount('reports','subject LIKE "%'.db()->quote($value).'%"'))
                        $this->flash[] = 'Please be more specific in your subject line.';
                    if(strlen($value) < 3 || strlen($value) > 255)
                        $this->flash[] = 'Subject lines must be between 3 and 255 characters.';

                    if(preg_match('/\S{25}/',$value))
                        $this->flash[] = 'Your subject line contains a single word over 25 characters in length. This can cause problems for certain browsers, and is not allowed.';

                    if(strtoupper($value) === $value)
                        $this->flash[] = 'CAPS LOCK IS CRUISE CONTROL FOR COOL';

                    break;
                case 'status':
                    if(!selectCount('statuses','id = '.(int)$value))
                        $this->flash[] = 'Please choose a valid initial status.';
                    break;
                case 'description':
                case 'reproduce':
                case 'expected':
                case 'actual':
                   // $value = strlen(trim(strip_tags(preg_replace('/&.+?;/', '',$value))));
                    if(strlen($value) < 2 || strlen($value) > 65535)
                        $this->flash[] = $field .' must be between 2 and 65,535 characters. Your '.$field.' is '.strlen($value);

                    if(strtoupper($value) === $value)
                        $this->flash[] = 'JESUS CHRIST STOP SHOUTING';

                   // if(preg_match('/\S{80}/',$value))
                   //     $this->flash[] = $field .' contains a single word over 80 characters in length. Stop it.';

            }
        }

        if(!empty($this->flash))
            exit;

        $this->data['params']['ip'] = remoteAddr();
        $this->data['params']['category'] = $this->data['category']['id'];

        $stmt = db()->prepare(
            'INSERT INTO reports (id,time,'.implode(',',array_keys($this->data['params'])).') VALUES (\'\',UNIX_TIMESTAMP(),:'.implode(',:',array_keys($this->data['params'])).')'
        );
        $stmt->execute($this->data['params']);
        header('Location: '.BUNZ_HTTP_DIR.'report/view/'.db()->lastInsertId());

    }
}

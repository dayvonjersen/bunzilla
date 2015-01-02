<?php
/**
 * post reports/comments 
 *
 * I'm not sure about the naming convention I've chosen here
 * index() and comment() make sense but category is ehh */
class post extends Controller
{
    // php class constants cannot contain expressions :\
    const ALLOWED_HTML = 'b|i|em|strong|dfn|samp|kbd|var|cite|del|ins|strike|s|u|ol|ul|li|dt|dd|dl|sup|sub|small|big|image|link';

    public function __construct()
    {
        !BUNZ_BUNZILLA_ALLOW_ANONYMOUS && $this->requireLogin();

        // probably need a better mechanism than this
        $where = 'ip = '.db()->quote(remoteAddr())
               .' AND time >= UNIX_TIMESTAMP() - 30';
        if(selectCount('reports',$where)||selectCount('comments',$where))
            $this->abort('stop spamming D:');

        parent::__construct();
    }

    public function index()
    {
        $this->tpl .= '/index';
        $this->data = [
            'categories' => db()->query(
                'SELECT id,title,caption,color,icon
                 FROM categories
                 ORDER BY title ASC'
            )->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    public function comment($id)
    {
        $this->tpl = 'error';
        if(!selectCount('reports','id = '.(int)$id))
            $this->abort('No such report!');

        $this->data['params'] = [
            'email' => filterOptions(1,'email'),
            'message' => filterOptions(0,'callback',null,
                [$this,'messageFilter']
            )
        ];
        $this->data['params'] = filter_input_array(
            INPUT_POST,
            $this->data['params']
        );


        $this->data['params']['report'] = (int)$id;
        $this->data['params']['ip'] = remoteAddr();

        $this->data['params']['epenis'] = (int)$this->auth();
        $sql = 'INSERT INTO comments 

         (id,time,'.implode(',',array_keys($this->data['params'])).')

                VALUES 

         (\'\',UNIX_TIMESTAMP(),:'.implode(',:',
            array_keys($this->data['params'])).')';

        $location = BUNZ_HTTP_DIR.'report/view/'.(int)$id;
        if(!empty($_POST))
        {
            if($this->createReport($sql))
            {
                $this->flash[] = 'Comment added.';
                $location .= '#reply-'.db()->lastInsertId();
            }
        }
        $_SESSION['flash'] = serialize($this->flash);
        $_SESSION['params'] = serialize($this->data['params']);
        header('Location: '.$location);        
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
                $this->data['params'][$field] = filterOptions(0,'callback',
                    null,[$this,'messageFilter']
                );
        }

        $this->data['params'] = filter_input_array(INPUT_POST,$this->data['params']);
        $this->data['params']['category'] = $this->data['category']['id'];

        $this->data['params']['ip'] = remoteAddr();
        $this->data['params']['epenis'] = (int)$this->auth();

        $sql = 'INSERT INTO reports 

        (id,time,'.implode(',',array_keys($this->data['params'])).')

                VALUES 

(\'\',UNIX_TIMESTAMP(),:'.implode(',:',array_keys($this->data['params'])).')';

        if(!empty($_POST))
        {
            if($this->createReport($sql))
                header('Location: '.BUNZ_HTTP_DIR.'report/view/'.db()->lastInsertId());
        }
    }

    /**
     * Callback to format/filter posts 
     * Taken straight from forum software I made as a teenager
     */
    private function messageFilter($msg)
    {
        $msg = htmlspecialchars($msg);
        $msg = trim(str_replace([chr(7),chr(160),chr(173)], '', $msg));
        $msg = preg_replace(
            '/&lt;(\/)?('.self::ALLOWED_HTML.')&gt;/i','<$1$2>',$msg
        );

        /**
         * Images!
         * @usage: <image>http://example.com/hello.jpg</image> */
        preg_match_all(
            '/\<image\>(.{1,})\<\/image\>/i',$msg,$images,PREG_SET_ORDER
        );
        foreach($images as $image)
        {
            // @getimagesize($image[1]))
            // ^ that used to work somehow
            // now I use this v
            $msg = str_replace(
                    $image[0],
                (   preg_match('/^http(s)?:\/\//', $image[1]) 

                ?   '<img src="'.$image[1].'" alt="Image could not be loaded.">'

                :   '&lt;!-- Invalid Image URL! --&gt;'

                ),
                    $msg
            );
        }

        /**
         * Links!
         * @usage: <link>http://example.com{Click Here!}</link> */
        preg_match_all(
            '/\<link\>(.+?)(\{(.*?)\})*\<\/link\>/i',$msg,$links,PREG_SET_ORDER
        );
        foreach($links as $link)
        {
            $location = preg_match('/^http(s)?:\/\//', $link[1])
                ? $link[1] : 'about:blank';
            $title = isset($link[3]) && strlen(trim($link[3])) 
                ? trim($link[3]) : $location;
            $title = strlen($title) > 70 
                ? substr($title,0,35).' ... '.substr($title,-15) : $title;

            $msg = str_replace(
                $link[0], 
                '<a href="'.$location.
                    '" title="'.$location.
                    '" class="icon-link"'.
                    '" target="_blank">'.$title.
                    '</a>',
                $msg
            );
        }

        // how to handle newlines is a bit of a pain, this works for now
        $msg = nl2br($msg);

        /**
         * Highlighted Code!
         * @usage: <code php><?= 'hello, world' ?></code>
         * highlight.js does the hard stuff */
        preg_match_all(
            '/\&lt;code(\s+[0-9a-z]+)?&gt;(.+)\&lt;\/code\&gt;/ims',
            $msg,$codes,PREG_SET_ORDER
        );
        foreach($codes as $code)
        {
            $code[1] = ( strlen($code[1]) 
                       ? ' class="language-'.trim($code[1]).'"'
                       : ''
                       );

            $code[2] = str_replace('<br />', '', $code[2]);
            $msg = str_replace(
                $code[0],
                '<pre><code'.$code[1].'>'.$code[2].'</code></pre>',
                $msg
            );
        }

        return $msg;
    }

    /**
     * not as nice as what's in admin.php */
    private function createReport($sql)
    {
        // force identity for logged in developers
        if($this->auth())
            $this->data['params']['email'] = 
                $_SERVER['PHP_AUTH_USER'].'@'.$_SERVER['SERVER_NAME'];

        // error checking ain't pretty
        // this isn't complete error checking for every field
        // maybe it should be
        foreach($this->data['params'] as $field => $value)
        {
            if(empty($value) && $value !== 0)
            {   $this->flash[] = $field .' cannot be blank.';
                continue;
            }
            switch($field)
            {
                case 'subject':
                    // sanitize the subject line
                    $this->data['params'][$field] = $value = trim(
                        str_replace([chr(7),chr(160),chr(173),"\n","\r","\t"],
                            '',
                            preg_replace('/\s{2,}/',' ',$value)
                        )
                    );

                    // check for duplicates 
                    if(selectCount('reports','subject LIKE "%'
                        .db()->quote($value).'%"'))
                        $this->flash[] = 
                            'Please be more specific in your subject line.';

                    // enforce length
                    if(strlen($value) < 3 || strlen($value) > 255)
                        $this->flash[] = 
                            $field.' must be between 3 and 255 characters.';

                    if(preg_match('/\S{25}/',$value))
                        $this->flash[] = $field .' contains a single word '
                            .'over 25 characters in length. This can cause '
                          .'problems for certain browsers and is not allowed.';

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
                case 'message':

// $value = strlen(trim(strip_tags(preg_replace('/&.+?;/', '',$value))));

// ^ this check doesn't count final message formatting against 
//   the maximum length, which is nice for users

//   however, the maximum length is actually the limit of the DB column
//   at the moment, anyway

                    if(strlen($value) < 2 || strlen($value) > 65535)
                        $this->flash[] = $field 
                            .' must be between 2 and 65,535 characters. Your '
                            .$field.' is '.strlen($value);

                    if(strtoupper($value) === $value)
                        $this->flash[] = 'JESUS CHRIST STOP SHOUTING';
                    break;
            }
        }

        // stop here on error
        if(!empty($this->flash))
            return false;

        // move zig
        $stmt = db()->prepare($sql);
        return($stmt->execute($this->data['params']));
    }
}

<?php
/**
 * post reports/comments 
 *
 * I'm not sure about the naming convention I've chosen here
 * index() and comment() make sense but category is ehh */
class post extends Controller
{
    // php class constants cannot contain expressions :\
    // this is no longer even the longest line which is sad
    const ALLOWED_HTML = 'b|i|kbd|del|ins|strike|s|u|ol|ul|li|dt|dd|dl|sup|sub|small|big|image|link|code';

    public function __construct()
    {

        parent::__construct();
        !BUNZ_BUNZILLA_ALLOW_ANONYMOUS && $this->requireLogin();
    }

    public $breadcrumbs = [];
    public function setBreadcrumbs($method)
    {
        
        $this->breadcrumbs[] = ['href' => 'report/index', 
                                'title' => 'Category Listing',
                                'icon'  => 'icon-ul'];

        $category = $this->data['category'];
        $this->data['category_id'] = $category['id'];

        $this->breadcrumbs[] = ['href' => 'report/category/'.$category['id'],
                                'title' => $category['title'],
                                'icon' => $category['icon']
        ];

        if($method == 'category')
        {
            $this->breadcrumbs[] = ['href' => 'post/category/'.$category['id'],
                                'title' => 'Submit New',
                                'icon' => 'icon-plus'];
        } else {

            $this->breadcrumbs[] = ['href' => 'report/view/'.$this->data['params']['report_id'],
                                    'title' => $this->data['params']['subject'],
                                    'icon' => 'icon-doc-text-inv'];
            $this->breadcrumbs[] = ['href' => 'post/edit/'.$this->data['params']['report_id'],
                                    'title' => 'Edit',
                                    'icon' => 'icon-pencil-alt'];
        }
        return;
    }

    private function spamCheck()
    {
        if(!empty($_POST) && !$this->auth())
        {
        // probably need a better mechanism than this
        $where = 'ip = '.db()->quote(remoteAddr())
               .' AND time >= UNIX_TIMESTAMP() - 30';
        if(selectCount('reports',$where)||selectCount('comments',$where))
            $this->abort('stop spamming D:');
        }
    }

    public function index()
    {
        $this->spamCheck();
        $this->tpl .= '/index';
        $this->data = [
            'categories' => db()->query(
                'SELECT id,title,caption,color,icon
                 FROM categories
                 ORDER BY title ASC'
            )->fetchAll(PDO::FETCH_ASSOC)
        ];
    }

    /**
     * useless feature, 
     * but prevents someone from deleting useful/damning information */
    protected function diff( $what, $text1, $text2, $type, $id )
    {
        if(!in_array($type,['reports', 'comments'],true))
            throw new InvalidArgumentException('$type must be a valid table');

        $orig = tempnam('/tmp',uniqid());
        $edit = tempnam('/tmp',uniqid());
        file_put_contents($orig, "$text1\n");
        file_put_contents($edit, "$text2\n");
        file_put_contents(
            BUNZ_DIR . 'diff/'.$type.'/'.(int)$id,
            "$what changed @ ".date('r')."\n".`diff -u $orig $edit`,
            FILE_APPEND | LOCK_EX
        );
        unlink($orig);
        unlink($edit);
    }

    public function edit($reportId, $commentId = false)
    {
        $this->tpl .= '/edit';
        $this->checkReport($reportId);
        $reportId = (int)$reportId;
        $this->data['params'] = current(db()->query(
            'SELECT category, subject'.($commentId===false?', description, reproduce, expected, actual, ip, email':'').'
             FROM reports
             WHERE id = '.$reportId
        )->fetchAll(PDO::FETCH_ASSOC));

        $this->setReportCategory($this->data['params']['category']);
        if($commentId !== false)
        {
            $commentId = (int)$commentId;
            if(!selectCount('comments',
                'id = '.$commentId.' AND report = '.$reportId))
                $this->abort('No such comment!');

            $this->data['params'] += current(db()->query(
                'SELECT ip, message, email FROM comments WHERE id = '.$commentId
            )->fetchAll(PDO::FETCH_ASSOC));

            $this->data['params']['comment_id'] = $commentId;
            $this->data['category']['message'] = true; // shut up
        } else {
            $this->data['params']['tags'] = [];
            foreach(db()->query(
            'SELECT tag
             FROM tag_joins 
             WHERE report = '.$reportId)->fetchAll(PDO::FETCH_NUM) as $tag)
                $this->data['params']['tags'][] = $tag[0];
        }

        $this->data['params']['report_id'] = $reportId;

        if(!$this->auth() && !compareIP($this->data['params']['ip']))
            $this->abort('Access denied.');

        $usr = $this->data['params']['email'];
        unset($this->data['params']['email']);
        if(!empty($_POST))
        {
/***
XXX ::: what the fuck stop being a lazy shit
***/
            $filtOpts__obj = $this->getFilterOptions($commentId === false ? 'report' : 'comment');
            $filtOpts = array_intersect_key(
                $filtOpts__obj->options, $this->data['params']);
            $changes = filter_input_array(INPUT_POST, $filtOpts);
            $set = [];
            foreach($this->data['params'] as $field => $value)
            {
                if(!isset($changes[$field]) || $changes[$field] === $value)
                {    if(!isset($_POST['preview_report']))
                        unset($this->data['params'][$field]);
                }else {
                    $set[] = $field .' = :'.$field;
                    $this->data['params'][$field] = $changes[$field];
                    if(!isset($_POST['preview_report']))
                        $this->diff($field, $value, $changes[$field], $commentId === false ? 'reports' : 'comments', $commentId === false ? $reportId : $commentId);
                }
            }
            $this->previewReport();

            if(!count($set))
            {
                $this->flash[] = 'No changes were made.';
                $_SESSION['flash'] = serialize($this->flash);
                header('Location: '.BUNZ_HTTP_DIR.'/report/view/'.$reportId);
                exit;
            }

            $set[] = 'edit_time = :fux';
            $this->data['params']['fux'] = time();

            $sql = 'UPDATE '.($commentId === false ? 'report' : 'comment').'s SET '.implode(', ',$set).' WHERE id = '.($commentId === false ? $reportId : $commentId);
            if($this->createReport($sql))
            {
                if($commentId === false)
                    Statuslog::create('report', $reportId, 'made an edit', $this->auth() ? $_SERVER['PHP_AUTH_USER'] : $usr);

                $this->flash[] = 'Your desired changes were made.';
                $_SESSION['flash'] = serialize($this->flash);
                header('Location: '.BUNZ_HTTP_DIR.'report/view/'.$reportId.($commentId !== false? '#reply-'.$commentId : ''));
                exit;
            }
        }
           
        $this->setBreadcrumbs(__FUNCTION__);
    }

    private function checkReport($id)
    {
        if(!selectCount('reports','id = '.(int)$id))
            $this->abort('No such report!');
    }

    private function getFilterOptions($mode = 'report')
    {
        $filtOpts = new Filter();
        $filtOpts->addEmail();
//        $filtOpts = [];

//        $filtOpts['email'] = Filter::_filterOptions(1,'email');

        $callback = [$this,'messageFilter'];
        if($mode === 'comment')
        {
//            $filtOpts['message'] = Filter::_filterOptions(0,'callback',null,[$this,'messageFilter']);
            $filtOpts->addCallback('message',$callback);
            return $filtOpts;
        }

//        $filtOpts['subject'] = Filter::_filterOptions(0,'full_special_chars');
        $filtOpts->addString('subject');
        //$filtOpts['status']  = Filter::_filterOptions(0,'number_int');
        foreach(['description','reproduce','expected','actual'] as $field)
        {
            if($this->data['category'][$field])
            {
//                $filtOpts[$field] = Filter::_filterOptions(0,'callback',null,[$this,'messageFilter']);
                $filtOpts->addCallback($field, $callback);
            }
        }

        return $filtOpts;       
    }

    private function setReportCategory($id)
    {
        $result = db()->query(
            'SELECT *
             FROM categories 
             WHERE id = '.(int)$id
        );
        if(!$result->rowCount())
            $this->abort('No such category!');

        $this->data['category'] = $result->fetch(PDO::FETCH_ASSOC);
    }

    public function comment($id)
    {
        $this->spamCheck();
        $this->tpl = 'error';
        $this->checkReport($id);

        $filtOpts = $this->getFilterOptions('comment');
//        $this->data['params'] = filter_input_array(INPUT_POST,$filtOpts);
        $this->data['params'] = $filtOpts->input_array();
        // force identity for logged in developers
        if($this->auth())
            $this->data['params']['email'] = 
                $_SERVER['PHP_AUTH_USER'].'@'.$_SERVER['SERVER_NAME'];
        $this->data['params']['report'] = (int)$id;
        $this->data['params']['ip'] = remoteAddr();

        $this->data['params']['epenis'] = (int)$this->auth();
        /**
         * advanced quote replies (tm) */
        if(preg_match_all('/\&gt;\&gt;(\d+)/ms', $this->data['params']['message'], $quotes, PREG_SET_ORDER))
        {
            if(count($quotes) > 1)
                $this->flash[] = 'At this time, you can only reply to one comment at a time (sorry!)';
            else {
            foreach($quotes as $quote)
            {
                $reply_to = (int)$quote[1];
                if(selectCount('comments', 'id = '.$reply_to))
                {
                    $this->data['params']['message'] = str_replace(
                        $quote[0],
                        '<a href="#reply-'.$reply_to.'">&gt;&gt;'.$reply_to.'</a>',
                        $this->data['params']['message']
                    );
                    $this->data['params']['reply_to'] = $reply_to;
                }
            }}
        }
        $sql = 'INSERT INTO comments 

         (id,time,'.implode(',',array_keys($this->data['params'])).')

                VALUES 

         (\'\',UNIX_TIMESTAMP(),:'.implode(',:',
            array_keys($this->data['params'])).')';

        $location = BUNZ_HTTP_DIR.'report/view/'.(int)$id;
        if(!empty($_POST))
        {
            $this->previewReport();
            if($this->createReport($sql))
            {
                $this->flash[] = 'Comment added.';
                $location .= '#reply-'.db()->lastInsertId();

                if($this->auth() && isset($_POST['changelog']))
                    Changelog::append($this->data['params']['message']);
            } else {
                $_SESSION['params'] = serialize($this->data['params']);
                $location .= '#comment';
            }
        }
        $_SESSION['flash'] = serialize($this->flash);
        header('Location: '.$location);        
    }

    private function handleTags($reportId)
    {
        if(isset($_POST['tags']) && is_array($_POST['tags']))
        {
            $tags = [];
            foreach($_POST['tags'] as $tag)
                if(selectCount('tags','id = '.(int)$tag))
                    $tags[] = '('.(int)$reportId.','.(int)$tag.')';

            if(!empty($tags))
                db()->query('INSERT INTO tag_joins (report,tag) VALUES '.implode(',',$tags));
        }
    }

    public function category($id)
    {
        $this->spamCheck();
        $this->tpl .= '/category';
        $this->setReportCategory($id);
        $this->data['tags'] = db()->query('SELECT id FROM tags ORDER BY title ASC')->fetchAll(PDO::FETCH_ASSOC);

        $filtOpts = $this->getFilterOptions('report');

        $this->data['params'] = $filtOpts->input_array();
        // force identity for logged in developers
        if($this->auth())
            $this->data['params']['email'] = 
                $_SERVER['PHP_AUTH_USER'].'@'.$_SERVER['SERVER_NAME'];
        $this->data['params']['category'] = $this->data['category']['id'];
        $this->data['params']['status'] = db()->query('SELECT id FROM statuses WHERE `default` = 1')->fetchColumn();

        $this->data['params']['ip'] = remoteAddr();
        $this->data['params']['epenis'] = (int)$this->auth();
        $this->data['params']['priority'] = db()->query('SELECT id FROM priorities WHERE `default` = 1')->fetchColumn();

        $sql = 'INSERT INTO reports 
            (id,time,'.implode(',',array_keys($this->data['params'])).')
                VALUES 
            (\'\',UNIX_TIMESTAMP(),:'
            .implode(',:',array_keys($this->data['params'])).')';

        if(!empty($_POST))
        {
            $this->previewReport();
            if($this->createReport($sql))
            {
                $reportId = db()->lastInsertId();
                $this->handleTags($reportId);
                $this->flash[] = 'Report submitted!';
                $_SESSION['flash'] = serialize($this->flash);
                header('Location: '.BUNZ_HTTP_DIR.'report/view/'.$reportId);
            }
        }
        $this->setBreadcrumbs(__FUNCTION__);
    }

    /**
     * Callback to format/filter posts 
     * Taken straight from forum software I made as a teenager
     */
    public static function messageFilter($msg)
    {
        $msg = htmlspecialchars($msg);
        $msg = trim(str_replace([chr(7),chr(160),chr(173)], '', $msg));
        if(isset($_POST['disable_html']))
            return $msg;

        while(preg_match('/(\[|&lt;)('.self::ALLOWED_HTML.')(\]|&gt;)(.*?)\1\/\2\3/misu',$msg))
            $msg = preg_replace(
                '/(\[|&lt;)('.self::ALLOWED_HTML.')(\]|&gt;)(.*?)\1\/\2\3/misu','<$2>$4</$2>',$msg
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

                ?   '<img src="'.$image[1].'" alt="Image could not be loaded." data-caption="'.$image[1].'">'

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
                    ' target="_blank">'.$title.
                    '</a>',
                $msg
            );
        }

        // how to handle newlines is a bit of a pain, this works for now
        // but disabling them is a fix
        if(!isset($_POST['disable_nlbr']) || !$_POST['disable_nlbr'])
            $msg = nl2br($msg);

        /**
         * Highlighted Code!
         * @usage: <code php><?= 'hello, world' ?></code>
         * highlight.js does the hard stuff */
        preg_match_all(
            '/\<code(\s+[0-9a-z]+)?\>(.+?)\<\/code\>/ims',
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
        // error checking ain't pretty
        // this isn't complete error checking for every field
        // maybe it should be
        foreach($this->data['params'] as $field => $value)
        {

            if($value !== 0 && in_array($field, ['subject', 'email', 'message']) && empty($value))
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
                    if(selectCount('reports','subject LIKE '
                        .db()->quote('%'.$value.'%').''))
                        $this->flash[] = 
                            'Please be more specific in your subject line.';


// you know what you doing
if(!$this->auth())
{
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
}
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

// you know what you doing
if(!$this->auth())
{
                    if(strtoupper($value) === $value)
                        $this->flash[] = 'JESUS CHRIST STOP SHOUTING';
                
}
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

    /**
     * let's see if I can shoehorn this in here */
    private function previewReport()
    {
        if(isset($_POST['preview_report']))
        {
            $this->tpl = 'post/preview';
            exit;
        }
    }
}

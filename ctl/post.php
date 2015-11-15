<?php
/**
 * post reports/comments 
 */
class post extends Controller
{
    /**
     * what tags are allowed in posts */
    const ALLOWED_HTML = 'b|i|kbd|del|ins|strike|s|u|ol|ul|li|dt|dd|dl|sup|sub|small|big|image|link|code|p|strong|em';

    /**
     * minimum time (in seconds) allowed between posts to cut down on spam */
    const SPAM_TIMEOUT = 30;

    /**
     * Common to public methods */
    // every posting method can be used without logging in,
    // but only if you set allow_anonymous to 1 in res/settings.ini
    public function __construct()
    {
        parent::__construct();
        !BUNZ_BUNZILLA_ALLOW_ANONYMOUS && $this->requireLogin();
    }

    /**
     *
     * Public-facing methods/routes 
     *
     */

    // index: unused
    public function index()
    {
        $this->abort();
    }

    // edit: 2 args = comment, 1 arg = report
    public function edit($reportId, $commentId = false)
    {
        $editMode = $commentId === false ? 'report' : 'comment';

        $this->tpl .= '/edit';

        // verify the target exists
        $this->checkReport($reportId);
        $reportId  = (int) $reportId;
        $commentId = (int) $commentId;
        if($editMode == 'comment' && !selectCount(
            'comments','id = '.$commentId.' AND report = '.$reportId
        ))
            $this->abort('No such comment!');

        // grab datas
        $fields = ['category','subject'];
        if($editMode == 'report')
            $fields = array_merge(
                $fields,
                ['description','reproduce','expected','actual',
                 'INET6_NTOA(ip) AS ip','email']
            );

        $this->data['params'] = db()->query(
            'SELECT '.implode(',',$fields).'
             FROM reports
             WHERE id = '.$reportId
        )->fetch(PDO::FETCH_ASSOC);
        $this->data['params']['report_id'] = $reportId;
        $this->setReportCategory($this->data['params']['category']);

        // additional pylons
        if($editMode == 'comment')
        {
            // note this will overwrite the above ip and email in $params
            $this->data['params'] = array_replace(
                $this->data['params'],
                db()->query(
                    'SELECT INET6_NTOA(ip) AS ip, message, email 
                     FROM comments 
                     WHERE id = '.$commentId
                )->fetch(PDO::FETCH_ASSOC)
            );
            $this->data['params']['comment_id'] = $commentId;
            // needed for the view to display the field
            $this->data['category']['message']  = true; 
        } else {
            $this->data['params']['tags'] = db()->query(
                'SELECT tag
                 FROM tag_joins 
                 WHERE report = '.$reportId
            )->fetchAll(PDO::FETCH_COLUMN);
        }

        if(!$this->auth() && remoteAddr() != $this->data['params']['ip'])
            $this->abort('Access denied.');

        $usr = $this->data['params']['email'];

        unset($this->data['params']['email'],$this->data['params']['ip']);
        if(!empty($_POST))
        {
            $filt    = $this->getFilter($editMode);
            $changes = $filt->input_array();
            $set     = [];
            foreach($this->data['params'] as $field => $value)
            {
                if(!isset($changes[$field]) || $changes[$field] === $value)
                {    
                    if(!isset($_POST['preview_report']))
                        unset($this->data['params'][$field]);
                } else {
                    $set[] = $field .' = :'.$field;
                    $this->data['params'][$field] = $changes[$field];
                    if(!isset($_POST['preview_report']))
                    {
                        $this->diff(
                            $field, 
                            $value, 
                            $changes[$field], 
                            $editMode.'s', 
                            $editMode == 'report' ? $reportId : $commentId
                        );
                    }
                }
            }

            $this->previewReport();

            if($editMode == 'report' && $this->handleTags($reportId,true))
            {
                $this->flash[] = 'Tags updated.';
            }

            if(!count($set))
            {
                $this->redirectWithMessage(
                    'report/view/'.$reportId,
                    'No changes were made.'
                );
            }

            $set[] = 'edit_time = :edit_time';
            $this->data['params']['edit_time'] = time();

            $sql = 'UPDATE '.$editMode.'s 
                    SET '.implode(', ',$set)
                 .' WHERE id = '.($editMode == 'report' ? $reportId : $commentId);
            if($this->createReport($sql))
            {
                if($editMode == 'report')
                {
                    Statuslog::create(
                        'report', $reportId, 'made an edit', 
                         $this->auth() ? $_SERVER['PHP_AUTH_USER'] : $usr
                    );
                }

                $this->redirectWithMessage(
                    'report/view/'.$reportId
                        .($editMode == 'comment' ? '#reply-'.$commentId : ''),
                    'Your desired changes were made.'
                );
            }
        }
           
        $this->setBreadcrumbs(__FUNCTION__);
    }

    // comment: posts a new comment to a report specified by $id
    public function comment($id)
    {
        $this->spamCheck();
        $this->checkReport($id);

        $filt = $this->getFilter('comment');
        $this->data['params'] = $filt->input_array();

        // force identity for logged in developers
        if($this->auth())
            $this->data['params']['email'] = 
                $_SERVER['PHP_AUTH_USER'].'@'.$_SERVER['SERVER_NAME'];

        $this->data['params']['report'] = (int)$id;
        $this->data['params']['epenis'] = (int)$this->auth();

        /**
         * advanced quote replies (tm) */
        if(preg_match_all('/\&gt;\&gt;(\d+)/ms', 
            $this->data['params']['message'], 
            $quotes, PREG_SET_ORDER))
        {
            if(count($quotes) > 1)
            {
                $this->flash[] = 'At this time, you can reply to only
                 one comment at a time (sorry!)';
            } else {
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
                }
            }
        }
        $sql = 'INSERT INTO comments 
                (id,time,ip,'
                    .implode(',', array_keys($this->data['params'])).')
                VALUES 
                (\'\',UNIX_TIMESTAMP(),INET6_ATON(:ip),:'
                    .implode(',:',array_keys($this->data['params']))
        .')';

        $this->data['params']['ip']     = remoteAddr();

        $message  = null;
        $location = 'report/view/'.(int) $id;
        if(!empty($_POST))
        {
            $this->previewReport();
            if($this->createReport($sql))
            {
                $message   = 'Comment added.';
                $location .= '#reply-'.db()->lastInsertId();

                if($this->auth() && isset($_POST['changelog']))
                    Changelog::append($this->data['params']['message']);
            } else {
                unset($this->data['params']['email'],$this->data['params']['ip']);
                $_SESSION['params'] = serialize($this->data['params']);
                $location .= '#comment';
            }
        } else {
            $this->captcha();
        }

        $this->redirectWithMessage($location,$message);
    }

    // category: posts a report to a category
    public function category($id)
    {
        $this->spamCheck();
        $this->tpl .= '/category';
        $this->setReportCategory($id);

        $this->data['tags'] = db()->query(
            'SELECT id FROM tags ORDER BY title ASC'
        )->fetchAll(PDO::FETCH_ASSOC);

        $filt = $this->getFilter('report');

        $this->data['params'] = $filt->input_array();

        // force identity for logged in developers
        if($this->auth())
            $this->data['params']['email'] = 
                $_SERVER['PHP_AUTH_USER'].'@'.$_SERVER['SERVER_NAME'];

        $this->data['params']['epenis']   = (int) $this->auth();

        $this->data['params']['category'] = $this->data['category']['id'];

        // initial status and priority
        $this->data['params']['status']   = db()->query(
            'SELECT id FROM statuses   WHERE `default` = 1'
        )->fetchColumn();
        $this->data['params']['priority'] = db()->query(
            'SELECT id FROM priorities WHERE `default` = 1'
        )->fetchColumn();

        $sql = 'INSERT INTO reports 
            (id,time,ip,'.implode(',',array_keys($this->data['params'])).')
                VALUES 
            (\'\',UNIX_TIMESTAMP(),INET6_ATON(:ip),:'
            .implode(',:',array_keys($this->data['params'])).')';

        $this->data['params']['ip'] = remoteAddr();
        if(!empty($_POST))
        {
            $this->previewReport();
            if($this->createReport($sql))
            {
                $reportId = db()->lastInsertId();
                $this->handleTags($reportId);

                $this->redirectWithMessage(
                    'report/view/'.$reportId,
                    'Report submitted!'
                );
            }
        } else {
            $this->captcha();
        }

        $this->setBreadcrumbs(__FUNCTION__);
    }

    /**
     * Helper functions and abstractions from here to EOF
     */

    // this breadcrumb thing feels out of place
    public $breadcrumbs = [];
    private function setBreadcrumbs($method)
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
            if(!isset($this->data['params']['report_id'])) {
                // this shouldn't happen but it does anyway fff TODO XXX D:
                return;
            }
            $this->breadcrumbs[] = ['href' => 'report/view/'.$this->data['params']['report_id'],
                                    'title' => $this->data['params']['subject'],
                                    'icon' => 'icon-doc-text-inv'];
            $this->breadcrumbs[] = ['href' => 'post/edit/'.$this->data['params']['report_id'],
                                    'title' => 'Edit',
                                    'icon' => 'icon-pencil-alt'];
        }
    }

    // probably need a better mechanism than this
    private function spamCheck()
    {
        if(!empty($_POST) && !$this->auth())
        {
            $where = 'ip = INET6_ATON('.db()->quote(remoteAddr()).')'
                   .' AND time >= UNIX_TIMESTAMP() - '.self::SPAM_TIMEOUT;
            if(selectCount('reports',$where)||selectCount('comments',$where))
                $this->abort('stop spamming D:');
        }
    }


    /**
     * really an unnecessary feature, but it prevents someone 
     * from permanently deleting potentially useful information */
    private function diff( $what, $text1, $text2, $type, $id )
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

    private function checkReport($id)
    {
        if(!selectCount('reports','id = '.(int)$id))
            $this->abort('No such report!');
    }

    private function getFilter($mode = 'report')
    {
        $filt = new Filter();
        $filt->addEmail();

        $callback = [$this,'messageFilter'];
        if($mode === 'comment')
        {
            $filt->addCallback('message',$callback);
            return $filt;
        }

        $filt->addString('subject');
        foreach(['description','reproduce','expected','actual'] as $field)
            if($this->data['category'][$field])
                $filt->addCallback($field, $callback);

        return $filt;       
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

    private function handleTags($reportId,$editMode = false)
    {
        $rows = 0;
        if($editMode)
        {
            $previous_tags = db()->query(
                'SELECT tag FROM tag_joins WHERE report = '.$reportId
            )->fetchAll(PDO::FETCH_COLUMN);
            $keep_tags = [];
        } 
        if(isset($_POST['tags']) && is_array($_POST['tags']) && !empty($_POST['tags']))
        {
            $tags = [];
            foreach($_POST['tags'] as $tag)
            {
                if(selectCount('tags','id = '.(int)$tag))
                {
                    if($editMode)
                    {
                        $keep_tags[] = (int)$tag;
                        if(in_array($tag,$previous_tags))
                            continue;
                    }
                    $tags[] = '('.(int)$reportId.','.(int)$tag.')';
                }
            }

            if(!empty($tags))
                $rows += db()->query(
                    'INSERT INTO tag_joins (report,tag) 
                     VALUES '.implode(',',$tags)
                )->rowCount();
            if($editMode && count($keep_tags))
                $rows += db()->query(
                    'DELETE FROM tag_joins 
                     WHERE report = '.$reportId
                        .' AND tag NOT IN ('.implode(',',$keep_tags).')'
                )->rowCount();
        } elseif($editMode) {
            $rows += db()->query('DELETE FROM tag_joins WHERE report = '.$reportId)->rowCount();
        }
        return $rows;
    }

    /**
     * Callback to format/filter posts 
     * Taken straight from forum software I made as a teenager
     */
    public static function messageFilter($msg)
    {
        $msg = htmlspecialchars($msg);
        $msg = trim(str_replace([chr(7),chr(160),chr(173)], '', $msg));

        // how to handle newlines is a bit of a pain, this works for now
        // but disabling them is a fix
        if(!isset($_POST['disable_nlbr']) || !$_POST['disable_nlbr'])
            $msg = nl2br($msg);

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

        /**
         * Highlighted Code!
         * @usage: <code php><?= 'hello, world' ?></code>
         * highlight.js does the hard stuff */
        preg_match_all(
            '/(<|&lt;)code(\s+[0-9a-z]+)?(>|&gt;)(.+?)(<|&lt;)\/code(>|&gt;)/ims',
            $msg,$codes,PREG_SET_ORDER
        );
        foreach($codes as $code)
        {
            $code[2] = ( strlen($code[2]) 
                       ? ' class="language-'.trim($code[2]).'"'
                       : ''
                       );

            $code[4] = str_replace('<br />', '', $code[4]);
            $msg = str_replace(
                $code[0],
                '<pre><code'.$code[2].'>'.$code[4].'</code></pre>',
                $msg
            );
        }

        return $msg;
    }
 
    private function createReport($sql)
    {
        // error checking ain't pretty
        // this isn't complete error checking for every field
        // maybe it should be
        foreach($this->data['params'] as $field => $value)
        {
            if($value !== 0 && in_array($field, ['subject', 'email', 'message']) && empty($value))
            {   
                $this->flash[] = $field .' cannot be blank.';
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

                case 'description':
                case 'reproduce':
                case 'expected':
                case 'actual':
                case 'message':

/**
 * note, an aside:
 *
 * strlen(trim(strip_tags(preg_replace('/&.+?;/', '',$value))));
 *
 * this check wouldn't count final message formatting against 
 *   the maximum length, which is nice for users 
 *
 *   however, the maximum length is actually the limit of the DB column
 *   at the moment, anyway 
 */
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

        // stop here for robots
        $this->captcha();

        // move zig
        $stmt = db()->prepare($sql);
        return($stmt->execute($this->data['params']));
    }

    /**
     * let's see if I can shoehorn this in here */
    // update: 4/21/2015 6:27:31 PM I *can* shoehorn this in here.
    private function previewReport()
    {
        if(isset($_POST['preview_report']))
        {
            $this->tpl = 'post/preview';
            exit;
        }
    }

    private function captcha()
    {
        if(BUNZ_BUNZILLA_REQUIRE_CAPTCHA && !$this->auth())
        {
            if(empty($_POST))
            {
                captcha::set();
            } elseif(isset($_SESSION['captcha'],$_POST['captcha'])) {
                $answer = md5(strtolower(trim($_POST['captcha'])));
                if(!in_array($answer,$_SESSION['captcha']->a))
                {
                    captcha::set();
                    $this->flash[] = 'Sorry, you answered the captcha incorrectly!';
                    exit;
                } else {
                    unset($_SESSION['captcha']);
                }
            } else {
                captcha::set();
                $this->flash[] = 'You forgot to fill in the captcha!';
                exit;
            }
        }
    }
}

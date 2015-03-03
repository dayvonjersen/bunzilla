<?php
define('SITE_URL',
    sprintf('http%s://%s%s',
        isset($_SERVER['HTTPS']) ? 's' : '',
        $_SERVER['HTTP_HOST'],
        BUNZ_HTTP_DIR
    )
);
define('RSS_DATE_FORMAT','D, j M Y h:i:s T');

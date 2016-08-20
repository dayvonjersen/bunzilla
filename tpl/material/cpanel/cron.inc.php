<section id="cron" class="section">
    <blockquote class="z-depth-5">
        <p>There is a script to delete old reports, repair and optimize the database, and collect statistics in <code>res/cron.php</code></p>
        <p>In the future, there may be an option to configure this script to run automatically.</p>
        <p><strong>If you wish to setup this script to run here's what you can do:</strong></p>
            <ul>
                <li>edit line 11 of cron.php and set CRON_DEBUG_MODE to false</li>
                <li>do chmod 0777 on cron.php or make sure it has execute permissions for <?= `whoami` ?></li>
                <li>run crontab -e as <?= `whoami` ?></li>
            </ul>
        <p>Alternatively, especially if you're on windows where the above "whoami" shell call will have produced errors on this page, you can run the script from the command line using php-cli.</p>
        <p>If you don't know what some of the words on this page mean, google them.</p>
    </blockquote>
</section>

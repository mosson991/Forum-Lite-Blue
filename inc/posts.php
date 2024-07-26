<?php
if (!isset($_GET['tid'])) {
    header('location:index.php');
    exit();
} else {
    $tid = $_GET['tid'];
}
require('inc/header.php');
require_once('pagination.php');

// Increment view count
$db->exec("UPDATE topics SET views = views + 1 WHERE tid = $tid");

$sql = "SELECT fid, subject, locked, userid, views FROM topics WHERE tid = $tid";
$topic = $db->query($sql)->fetch();
$fid     = $topic->fid;
$subject = $topic->subject;
$locked  = $topic->locked;
$views   = $topic->views;
$originalPosterid = $topic->userid;

$sql = "SELECT fname FROM forums WHERE fid=$fid";
$fname = $db->query($sql)->fetchColumn();

$sql = "SELECT username FROM users WHERE uid=$originalPosterid";
$originalPoster = $db->query($sql)->fetchColumn();
?>
<div id="breadcrumb">
    <a href="index.php">Home</a> &#187;
    <a href="?act=topics&fid=<?php echo $fid ?>"><?php echo $fname ?></a> &#187;
    <a href="?act=posts&tid=<?php echo $tid ?>"><?php echo $subject ?></a>
    <span style="float:right"><a href="#bottom">#Bottom</a></span>
</div>

<?php
if (defined('UNAME') && !$locked) {
?>
<div id="submitlink">
    <a href="?act=newpost&tid=<?php echo $tid ?>">Post Reply</a>
</div>
<?php
} elseif (defined('ULEVEL') && ULEVEL > 0 && $locked) {
?>
<div id="submitlink">
    <a href="?act=newpost&tid=<?php echo $tid ?>">Post Reply</a> (Locked)
</div>
<?php
} elseif (defined('UNAME')) {
?>
<div id="submitlink">
    Locked topic
</div>
<?php
}

require 'markdown/autoload.php';
$parser = new \cebe\markdown\GithubMarkdown();

// Count total posts
$sql = "SELECT COUNT(*) FROM posts WHERE tid=$tid";
$totalPosts = $db->query($sql)->fetchColumn();

// Set items per page and get current page
$itemsPerPage = 10;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Create pagination object
$pagination = new Pagination($totalPosts, $itemsPerPage, $currentPage, "?act=posts&tid=$tid&page={page}");

echo '<div id="posts">';

// Fetch paginated posts
$offset = $pagination->getOffset();
$sql = "SELECT * FROM posts WHERE tid=$tid ORDER BY posttime ASC LIMIT $itemsPerPage OFFSET $offset";
foreach($db->query($sql) as $post) {
    $date = date('Y-m-d H:i', $post->posttime);
    $userid = $post->userid;
    $sql = "SELECT username FROM users WHERE uid=$userid";
    $username = $db->query($sql)->fetchColumn();
    echo '<div class="posthead">'.$date.' '.$username.' Views: '.$views.'</div>'."\n";
    $message = $parser->parse($post->message);
    echo '<div class="postbody">'.$message.'</div>'."\n";
}

// Display pagination links
$links = $pagination->getLinks();
echo '<div class="pagination">';
foreach ($links as $page => $url) {
    if (is_numeric($page)) {
        echo $page == $currentPage ? "<strong>$page</strong> " : "<a href='$url'>$page</a> ";
    } else {
        echo "<a href='$url'>$page</a> ";
    }
}
echo '</div>';

echo '<div id="bottom"></div>';
echo '</div>';

$db = null;
$parser = null;
require('inc/footer.php');
exit();

<?php
if (!isset($_GET['fid'])) {
    header('location:index.php');
    exit();
} else {
    $fid = $_GET['fid'];
}
require('inc/header.php');
require_once('pagination.php');

$sql = "SELECT fname FROM forums WHERE fid=$fid";
$fname = $db->query($sql)->fetchColumn();
?>
<div id="breadcrumb">
    <a href="index.php">Home</a> &#187;
    <a href="?act=topics&fid=<?php echo $fid ?>"><?php echo $fname ?></a>
</div>
<?php
if (defined('UNAME')) {
?>
<div id="submitlink">
    <a href="?act=newtopic&fid=<?php echo $fid ?>">New Topic</a>
</div>
<?php
}
echo '<div id="topics">';
echo '<table cellspacing="0" cellpadding="0">'."\n";

// Display sticky topics (unchanged)
$sql = "SELECT * FROM topics WHERE fid=$fid AND sticky=1 ORDER BY lasttime ASC";
foreach($db->query($sql) as $topic) {
    $tid = $topic->tid;
    $subject = $topic->subject;
    $lasttime = $topic->lasttime;
    $sql = "SELECT userid FROM posts WHERE posttime=$lasttime";
    $userid = $db->query($sql)->fetchColumn();
    $sql = "SELECT username FROM users WHERE uid=$userid";
    $poster = $db->query($sql)->fetchColumn();
    echo '<tr><td>Sticky: <a href="?act=posts&tid='.$tid.'">'.$subject.'</a></td>';
    echo '<td class="topicdata">'.date('Y-m-d H:i',$lasttime).' ';
    echo $poster.'</td></tr>'."\n";
}

// Count total non-sticky topics
$sql = "SELECT COUNT(*) FROM topics WHERE fid=$fid AND sticky=0";
$totalTopics = $db->query($sql)->fetchColumn();

// Set items per page and get current page
$itemsPerPage = 20;
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Create pagination object
$pagination = new Pagination($totalTopics, $itemsPerPage, $currentPage, "?act=topics&fid=$fid&page={page}");

// Fetch paginated non-sticky topics
$offset = $pagination->getOffset();
$sql = "SELECT * FROM topics WHERE fid=$fid AND sticky=0 ORDER BY lasttime DESC LIMIT $itemsPerPage OFFSET $offset";
foreach($db->query($sql) as $topic) {
    $tid = $topic->tid;
    $subject = $topic->subject;
    $lasttime = $topic->lasttime;
    $sql = "SELECT userid FROM posts WHERE posttime=$lasttime";
    $userid = $db->query($sql)->fetchColumn();
    $sql = "SELECT username FROM users WHERE uid=$userid";
    $poster = $db->query($sql)->fetchColumn();
    echo '<tr><td><a href="?act=posts&tid='.$tid.'">'.$subject.'</a></td>';
    echo '<td class="topicdata">'.date('Y-m-d H:i',$lasttime).' ';
    echo $poster.'</td></tr>'."\n";
}

echo '</table>';

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

echo '</div>';

$db = null;
require('inc/footer.php');
exit();

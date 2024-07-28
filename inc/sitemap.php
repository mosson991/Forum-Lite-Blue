<?php
require('init.php');
$siteurl = "https://blight.line.pm/";

 header("Content-type: application/xml");
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Generate sitemap for forums
    $stmt = $db->query("SELECT fid FROM forums ORDER BY forder ASC");
    while ($row = $stmt->fetch()) {
        echo '<url>';
        echo '<loc>' . $siteurl . '?act=topics&amp;fid=' . htmlspecialchars($row->fid) . '</loc>';
        echo '<changefreq>weekly</changefreq>';
        echo '<priority>0.8</priority>';
        echo '</url>';
    }

    // Generate sitemap for topics
    $stmt = $db->query("SELECT tid FROM topics ORDER BY lasttime DESC");
    while ($row = $stmt->fetch()) {
        echo '<url>';
        echo '<loc>' . $siteurl . '?act=posts&amp;tid=' . htmlspecialchars($row->tid) . '</loc>';
        echo '<changefreq>weekly</changefreq>';
        echo '<priority>0.6</priority>';
        echo '</url>';
    }

    echo '</urlset>';
    exit();

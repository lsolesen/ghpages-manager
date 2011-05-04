<?php
// @todo Make sure that the filename has been cleaned up entirely
$filename = addslashes(strip_tags(request()->query('filename')));
if (!file_exists($GLOBALS['git_repository'] . $filename) || empty($filename)) {
  return response()->notFound();
}

if (request()->query('delete') == 'true') {
  if (unlink($GLOBALS['git_repository'] . $filename)) {
    flash_message($filename . ' has been deleted');
    return response()->seeOther(root_url());
  }
}

// todo split content so the jekyll specific header is in its own textarea
$content = file_get_contents($GLOBALS['git_repository'] . $filename);

$title = 'Content';

document()->setTitle($title);
document()->setLayout('ghpages');
?>
<h1><?php print $title; ?></h1>

<?php print $content; ?>

</body>
</html>


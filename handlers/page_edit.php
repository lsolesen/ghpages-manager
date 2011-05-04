<?php
// @todo Make sure that the filename has been cleaned up entirely
$filename = addslashes(strip_tags(request()->query('filename')));
if (!file_exists($GLOBALS['git_repository'] . $filename) || empty($filename)) {
  return response()->notFound();
}

if (request()->isPost()) {
  // @todo check content
  $content = request()->body('content');
  $yaml = request()->body('yaml');  

  // @todo check permissions
  if (file_put_contents($GLOBALS['git_repository'] . $filename, $yaml . $content)) {
    flash_message("Page updated successfully");
    response()->seeOther(root_url());
  }
}

// todo split content so the jekyll specific header is in its own textarea
$content = file_get_contents($GLOBALS['git_repository'] . $filename);
preg_match_all('/---.*?---/s', $content, $arr, PREG_PATTERN_ORDER);

if (!empty($arr[0][0])) {
  $yaml = $arr[0][0];
} else {
  $yaml = '';
}

$content = str_replace($yaml, '', $content);

document()->setTitle('Edit file');
document()->setLayout('ghpages');
document()->addScript('http://github.com/troelskn/upflow/raw/master/showdown.js');
document()->addScript('http://github.com/troelskn/upflow/raw/master/upflow.js');
document()->addStylesheet('http://github.com/troelskn/upflow/raw/master/upflow.css');
document()->addScript('http://github.com/troelskn/upflow/raw/master/blocktypes/image.js');
document()->addScript('http://github.com/troelskn/upflow/raw/master/blocktypes/ledger.js');
document()->addStylesheet(root_url() . '/res/main.css');
document()->addOnload('window.onload = function() {
  // Replace existing textarea with upflow editor
  upflow.attach(
    document.getElementById("canvas"),
    document.getElementById("content"));

  // Hide the original textarea
  document.getElementById("content").style.display = "none";
};
');
?>
<h1>Edit <?php e($filename); ?></h1>

<?php echo html_form_tag('post', url(page_edit_url($filename))); ?>
  <div id="canvas" class="upflow-canvas"></div>
  <textarea id="content" name="content"><?php print $content; ?></textarea>
  <!-- TODO Move move the dashes out and make the editing better -->
  <br />
  <label>YAML front matter</label>
  <textarea id="yaml" name="yaml"><?php print $yaml; ?></textarea>  
  <br />
  <input class="classy" type="submit" value="Save" /> <a class="button classy" href="<?php print root_url(); ?>"><span>Close</span></a>
<?php echo html_form_tag_end(); ?>


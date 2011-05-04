<?php
if (request()->isPost()) {
  // @todo clean up yaml
  $yaml = request()->body('yaml');
  // @todo clean up content
  $content = request()->body('content');
  // @todo clean up filename
  $filename = request()->body('filename');
  
  if (file_exists($GLOBALS['git_repository'] . $filename)) {
    flash_message('Page was already present');
    return response()->seeOther(page_url($filename));
  }
  // check permissions
  touch($GLOBALS['git_repository'] . $filename);

  if (file_put_contents($GLOBALS['git_repository'] . $filename, $yaml . $content)) {
    flash_message("Page is created successfully");
    response()->seeOther(root_url());
  }
}

document()->setTitle('Tester');
document()->setLayout('ghpages');
document()->addScript('http://github.com/troelskn/upflow/raw/master/showdown.js');
document()->addScript('http://github.com/troelskn/upflow/raw/master/upflow.js');
document()->addStylesheet('http://github.com/troelskn/upflow/raw/master/upflow.css');
document()->addScript('http://github.com/troelskn/upflow/raw/master/blocktypes/image.js');
document()->addScript('http://github.com/troelskn/upflow/raw/master/blocktypes/ledger.js');
document()->addStylesheet(root_url() . 'res/main.css');
document()->addOnload('window.onload = function() {
  // Replace existing textarea with upflow editor
  upflow.attach(
    document.getElementById("canvas"),
    document.getElementById("content"));

  // Hide the original textarea
  document.getElementById("content").style.display = "none";
};');
?>
<h1>Create file</h1>

<?php echo html_form_tag('post', url(root_url() . 'new_page')); ?>
  <!-- TODO: Check for filename via ajax -->
  <label>Filename <input type="text" value="" name="filename" /></label>
  <div id="canvas" class="upflow-canvas"></div>
  <textarea id="content" name="content"></textarea>
  <!-- TODO Move move the dashes out and make the editing better -->
  <br /><label>YAML front matter</label><br />
  <textarea id="yaml" name="yaml"></textarea>    
  <br />
  <input class="classy" type="submit" value="Save" /> <a class="button classy" href="<?php print root_url(); ?>"><span>Close</span></a>
<?php echo html_form_tag_end(); ?>

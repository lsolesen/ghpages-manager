<?php
/**
 * TODO FIXME AND MAKE ME BETTER
 */
function git($command, &$output = "") {
    $gitDir = $GLOBALS['git_repository'] . ".git";
    $gitWorkTree = $GLOBALS['git_repository'];
    $gitCommand = "git --git-dir=$gitDir --work-tree=$gitWorkTree $command";
    $output = array();
    $result;
    // FIXME: Only do the escaping and the 2>&1 if we're not in safe mode
    // (otherwise it will be escaped anyway).
    // FIXME: Removed escapeShellCmd because it clashed with author.
    $oldUMask = umask(0022);
    exec($gitCommand . " 2>&1", $output, $result);
    $umask = $oldUMask;
    // FIXME: The -1 is a hack to avoid 'commit' on an unchanged repo to fail.
    if ($result != 0) {
        // FIXME: HTMLify these strings
        print "<h2>Error</h2>\n<pre>\n";
        print "$" . $gitCommand . "\n";
        print join("\n", $output) . "\n";
        print "Error code: " . $result . "\n";
        print "</pre>";
        return 0;
    }
    return true;
}

if (request()->isPost()) {
  if (request()->body('commit')) {
    // TODO Make security measures for the commit message
    $message = request()->body('commit_message');
    if (git('commit -a -m "'.$message.'"', $output) === true) {
      flash_message('Changes was saved and are ready to be published');
    }    
  } elseif (request()->body('undo')) {
    if (git('reset --hard', $output) === true) {
      flash_message('Changes was undone');
    } 
  } elseif (request()->body('add')) {
    foreach (untracked_files() as $file) {
      if (git('add ' . $file, $output) === true) {
        flash_message($file . ' has been added');
      }    
    }
  }
}

class RecursiveFileIterator extends RecursiveIteratorIterator
{
    /**
     * Takes a path to a directory, checks it, and then recurses into it.
     * @param $path directory to iterate
     */
    public function __construct($path)
    {
        // Use realpath() and make sure it exists; this is probably overkill, but I'm anal.
        $path = realpath($path);
 
        if (!file_exists($path)) {
            throw new Exception("Path $path could not be found.");
        } elseif (!is_dir($path)) {
            throw new Exception("Path $path is not a directory.");
        }
        
        parent::__construct(new RecursiveIteratorIterator);
    }
} 

class RecursiveFileFilterIterator extends FilterIterator
{
    /**
     * acceptable extensions - array of strings
     */
    protected $ext = array();
    protected $dirs = array();
 
    /**
     * Takes a path and shoves it into our earlier class.
     * Turns $ext into an array.
     * @param $path directory to iterate
     * @param $ext comma delimited list of acceptable extensions
     */
    public function __construct($path, $ext = 'php')
    {
        $this->ext = explode(',', $ext);
        parent::__construct(new DirectoryIterator($path));
    }
 
    /**
     * Checks extension names for files only.
     */
    public function accept()
    {
        $item = $this->getInnerIterator();
 
        // If it's not a file, accept it.
        if (!$item->isFile()) {
            return false;
        }
 
        // If it is a file, grab the file extension and see if it's in the array.
        return in_array(pathinfo($item->getFilename(), PATHINFO_EXTENSION), $this->ext);
    }
}

function unstaged_files() {
  $output = array();
  git('diff --name-only', $output);
  return $output;
}

function untracked_files() {
  $output = array();
  git('ls-files --other --exclude-standard', $output);
  return $output;
}

/**
 * @todo FIXME and refactor together with git()
 */
function unpushed_files() {
  $gitDir = $GLOBALS['git_repository'] . ".git";
  $gitWorkTree = $GLOBALS['git_repository'];
  $gitCommand = "git --git-dir=$gitDir --work-tree=$gitWorkTree status | grep ahead";
  $output = array();
  // FIXME: Only do the escaping and the 2>&1 if we're not in safe mode
  // (otherwise it will be escaped anyway).
  // FIXME: Removed escapeShellCmd because it clashed with author.
  $oldUMask = umask(0022);
  exec($gitCommand . " 2>&1", $output, $result);
  $umask = $oldUMask;
  return $output;
}

document()->setLayout("ghpages");
document()->setTitle("Dashboard");
document()->addStylesheet('https://github.com/troelskn/krudt/raw/master/www/krudt.css');
document()->addStylesheet(root_url() . '/res/main.css');
?>
<h1>Dashboard</h1>

<?php if (count(unstaged_files()) > 0): ?> 
    <p><strong>Files has been changed but not saved yet</strong>: 
    <?php print implode(',', unstaged_files()); ?></p>
    <?php echo html_form_tag('post', url(root_url())); ?>
      <textarea id="commit_message" name="commit_message"></textarea>  
      <input class="classy" type="submit" name="commit" value="Save" /> <input class="classy" type="submit" name="undo_changes" value="Undo" />
    <?php echo html_form_tag_end(); ?>
<?php elseif (count(untracked_files()) > 0): ?>
    <p><strong>Files has been added, but not saved yet</strong>: 
    <?php print implode(',', untracked_files()); ?></p>
    <?php echo html_form_tag('post', url(root_url())); ?>
      <input class="classy" type="submit" name="add" value="Save" />
    <?php echo html_form_tag_end(); ?>

<?php elseif (count($unpushed = unpushed_files()) > 0): ?>
        <p><pre>Maybe it is time to publish your changes.
<?php e($unpushed[0]); ?></pre></p>        
<?php endif; ?>
<!-- 
<p>
  <strong>TODO</strong>: If unpushed commits it should be possible to push
</p>
-->
<!--
<p>
  <strong>TODO</strong>: If unpulled commits it should be possible to pull
</p>
-->
<h3>List with existing pages</h3>
<table class="collection">
    <thead>
    <tr>
        <th>Filename</th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    
    <tbody>
    <?php foreach (new RecursiveFileFilterIterator($GLOBALS['git_repository'], 'htm,html,txt,textile,md,markdown') as $item): ?>
    <?php if (is_dir($item)) continue; ?>
    <tr class="rowlink">
        <td><?php e($item->getFileName()); ?></td>
        <td class="actions"><?php print html_link(page_edit_url($item->getFileName()), 'edit'); ?></td>
        <td class="actions"><?php print html_link(page_url($item->getFileName() . '&delete=true'), 'delete'); ?></td>                
    </tr>
    <?php endforeach; ?>
    </tbody>

</table>

<p>
  <a class="button classy" href="<?php print page_new_url(); ?>"><span>Create new page</span></a>
</p>

<!--
<p>
    <strong>TODO</strong>: Make it possible to create posts also 
</p>
-->

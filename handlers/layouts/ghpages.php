<!DOCTYPE html>
<html>
  <head>
    <meta charset='utf-8'>

    <title><?php e($title); ?></title>
    <style type="text/css">
      body {
        margin-top: 1.0em;
        font-family: Helvetica, Arial, FreeSans, san-serif;
        color: #000000;
      }
      #container {
        margin: 0 auto;
        width: 850px;
      }
      h1 { font-size: 3.8em; margin-bottom: 3px; }
      h1 .small { font-size: 0.4em; }
      h1 a { text-decoration: none }
      h2 { font-size: 1.5em;  }
      h3 { text-align: center;  }
      pre { background: #000; color: #fff; padding: 15px;}
      hr { border: 0; width: 80%; border-bottom: 1px solid #aaa}
    </style>
    
<?php foreach ($stylesheets as $style): ?>
    <link rel="stylesheet" href="<?php e($style); ?>" />
<?php endforeach; ?>
<?php foreach ($scripts as $script): ?>
    <script type="text/javascript" src="<?php e($script); ?>"></script>
<?php endforeach; ?>
  </head>
  <body>
     <a href="http://github.com/lsolesen/ghpages-manager"><img style="position: absolute; top: 0; right: 0; border: 0;" src="http://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub" /></a>
    <div id="container">
    <?php foreach (get_flash_messages() as $flash): ?>
    <p class="flash-<?php e($flash['type']); ?>"><?php e($flash['message']); ?></p>
    <?php endforeach; ?>
    <?php echo $content; ?>
    </div>
  </body>
<?php foreach ($onload as $javascript): ?>
    <script type="text/javascript">
      <?php echo $javascript; ?>
    </script>
<?php endforeach; ?>
</html>

<?php
require_once '../bootstrap.inc.php';
require_once 'web.inc.php';
ob_start();
try {
  render_in_place(resolve_route(request_uri()));
  if (isset($GLOBALS['RESPONSE_DOCUMENT']['RENDER_LAYOUT'])) {
    $content = ob_get_clean();
    ob_start();
    render_in_place($GLOBALS['RESPONSE_DOCUMENT']['RENDER_LAYOUT'].'_layout', array_merge($GLOBALS['RESPONSE_DOCUMENT'], array('content' => $content)));
  }
  header("HTTP/1.1 ".$GLOBALS['HTTP_RESPONSE']['STATUS']);
  foreach ($GLOBALS['HTTP_RESPONSE']['HEADERS'] as $header) {
    header($header[0].": ".$header[1]);
  }
  ob_end_flush();
} catch (http_SeeOther $ex) {
  header("HTTP/1.1 303 See Other");
  header('Location: ' . $ex->getMessage());
} catch (http_NotModified $ex) {
  header("HTTP/1.1 304 Not Modified");
  foreach ($ex->getMessage() as $header) {
    header($header);
  }
} catch (http_NotFound $ex) {
  header("HTTP/1.1 404 Not Found");
  header('Content-Type: text/plain');
  echo "The requested page could not be found.\n";
  echo request_method(), " ", request_uri(), "\n\n";
  echo str_repeat(" ", 512);
} catch (Exception $ex) {
  header("HTTP/1.1 500 Internal Server Error");
  header('Content-Type: text/plain');
  echo "Error Resolving request\n\n";
  echo request_method(), " ", request_uri(), "\n\n";
  if ($GLOBALS['ENVIRONMENT'] === 'development') {
    echo $ex;
    echo str_repeat(" ", 512);
  }
}

<?php

use \Model\Post;

use \Doctrine\DBAL\Exception\UniqueConstraintViolationException;

$app->get('/', function($req, $res, $args) {
  return 'Hello World!';
});

$app->get('/api/posts/{id:[0-9]+}', function($req, $res, $args) {

  $post = $this->get('orm')->find('\Model\Post', $args['id']);

  if (!$post) {
    return $res->withJson([ 'err' => true ], 404);
  }

  return $res->withJson([
    'id' => $post->getId(),
    'title' => $post->getTitle(),
    'content' => $post->getContent()
  ]);
});

$app->get('/api/posts', function($req, $res, $args) {

  $posts = $this->get('orm')->getRepository('\Model\Post')->findAll();

  return $res->withJson(array_map(function($post) {
    return [
      'id' => $post->getId(),
      'title' => $post->getTitle(),
      'content' => $post->getContent()
    ];
  }, $posts));

});

$app->post('/api/posts', function($req, $res, $args) {

  $params = $req->getParsedBody();

  $newPostTitle = isset($params['title']) ? $params['title'] : null;
  $newPost = new Post($newPostTitle);

  $this->get('orm')->persist($newPost);

  try {

    $this->get('orm')->flush();

  // title already exists
  } catch (UniqueConstraintViolationException $e) {

    return $res->withJson([ 'err' => true, 'msg' => 'title already exists' ], 500);

  // other exceptions
  } catch (\Exception $e) {

    return $res->withJson([ 'err' => true, 'msg' => 'other error' ], 500);
  }

  return $res->withJson([
    'title' => $newPost->getTitle(),
    'content' => $newPost->getContent()
  ]);
});

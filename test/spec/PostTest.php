<?php

namespace test\spec;

use \Model\Post;

class PostTest extends \TestCase {

  public function testAddDbRow() {

    // given
    $this->em->persist(new Post());
    $this->em->flush();

    // when
    $post = $this->em->find('\Model\Post', 1);

    // then
    $this->assertEquals('New post', $post->getTitle());
    $this->assertEquals('Insert content here...', $post->getContent());
  }


  public function testNoRoute() {

    // when
    $this->client->get('/norouteforthis');

    // then
    $this->assertEquals(404, $this->client->response->getStatusCode());
  }


  public function testApiRoot() {

    // given
    $this->client->get('/');

    // when
    $body = $this->client->response->getBody();

    // then
    $this->assertEquals('Hello World!', $body);
  }


  public function testApiGetPost() {

    // given
    $this->em->persist(new Post('Test Post'));
    $this->em->flush();

    // when
    $this->client->get('/api/posts/1');
    $data = json_decode($this->client->response->getBody(), $toArray = true);

    // then
    $this->assertArraySubset([ 'title' => 'Test Post' ], $data);
  }


  public function testApiGetAllPosts() {

    // given
    $this->em->persist(new Post('Test Post'));
    $this->em->persist(new Post('Test Post 2'));
    $this->em->flush();

    // when
    $this->client->get('/api/posts');
    $data = json_decode($this->client->response->getBody(), $toArray = true);

    // then
    $this->assertEquals(2, count($data));
  }


  public function testApiAddPostPersist() {

    // given
    $this->client->post('/api/posts', [ 'title' => 'Test Post' ]);

    // when
    $post = $this->em->find('\Model\Post', 1);

    // then
    $this->assertEquals(1, $post->getId());
    $this->assertEquals('Test Post', $post->getTitle());
    $this->assertEquals('Insert content here...', $post->getContent());
  }


  public function testApiAddPostReturnData() {

    // given
    $this->client->post('/api/posts', [ 'title' => 'Test Post' ]);

    // when
    $data = json_decode($this->client->response->getBody(), $toArray = true);

    // then
    $this->assertArraySubset([
      'title' => 'Test Post',
      'content' => 'Insert content here...'
    ], $data);
  }


  // expect exception via annotiations
  /** @expectedException \Doctrine\DBAL\Exception\UniqueConstraintViolationException */
  public function testUniquePostTitle() {

    // given
    $this->em->persist(new Post('Test Post'));
    $this->em->persist(new Post('Test Post'));

    // when
    $this->em->flush();
  }


  public function testApiUniquePostTitle() {

    // given
    $this->em->persist(new Post('Test Post'));
    $this->em->flush();

    // when
    $this->client->post('/api/posts', [ 'title' => 'Test Post' ]);
    $data = json_decode($this->client->response->getBody(), $toArray = true);

    // then
    $this->assertArraySubset([
      'err' => true,
      'msg' => 'title already exists'
    ], $data);
  }
}

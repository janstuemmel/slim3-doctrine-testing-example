<?php

namespace Model;

/** @Entity @Table(name="posts") **/
class Post
{

  /** @Id @Column(type="integer") @GeneratedValue **/
  private $id;

  /** @Column(type="string", unique=true) **/
  private $title;

  /** @Column(type="text") **/
  private $content;


  public function __construct($title = null) {

    $this->title = 'New post';
    $this->content = 'Insert content here...';

    if ($title) {
      $this->title = $title;
    }
  }

  public function getId() {
    return $this->id;
  }

  public function getTitle() {
    return $this->title;
  }

  public function getContent() {
    return $this->content;
  }
}

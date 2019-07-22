<?php
namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
  function __construct()
  {
    
  }

  public function index($name, Request $request)
  {
    return new Response("Welcome {$name}");
  }
}
<?php

declare(strict_types=1);

namespace App\Application\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

abstract class Action
{
  protected $logger;
  protected $view;
  protected $request;
  protected $response;
  protected $args;

  public function __construct(LoggerInterface $logger, Twig $twig)
  {
    $this->logger = $logger;
    $this->view = $twig;
  }

  public function __invoke(Request $request, Response $response, $args): Response
  {
    $this->request = $request;
    $this->response = $response;
    $this->args = $args;
    return $this->action();
  }

  protected function render($template, $data = []): Response
  {
    return $this->view->render($this->response, $template, $data);
  }

  protected function redirect($urlName): Response
  {
    // REF: https://github.com/slimphp/Slim/issues/2945
    $routeContext = RouteContext::fromRequest($this->request);
    $url = $routeContext->getRouteParser()->urlFor($urlName);
    return $this->response->withHeader('Location', $url);
  }

  abstract protected function action(): Response;
}

<?php

namespace Drupal\cors_manager\Stack;

use Asm89\Stack\Cors;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CorsMiddleware implements HttpKernelInterface {

  /**
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $app;

  /**
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $_container;

  /**
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $app
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   */
  public function __construct(HttpKernelInterface $app, ContainerInterface $container) {
    $this->app = $app;
    $this->_container = $container;
  }

  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    $options = $this->_container->getParameter('cors.config');
    $config = $this->_container->get('config.factory')
      ->get('cors_manager.config');
    $options = array_merge($options, array_filter($config->get('global')));
    $overrides = $config->get('per_route_overrides');
    if (!empty($overrides)) {
      $search = array_filter($overrides, function ($current) use ($request) {
        return $current['routeName'] == $request->getPathInfo();
      });

      if (!empty($search)) {
        $override = reset($search);
        if (!empty($override)) {
          $options = $this->_container->getParameter('cors.config');
          $options = array_merge($options, array_filter($config->get('global')));
          $options = array_merge($options, array_filter($override));
        }
      }
    }
    $cors = new Cors($this->app, $options);
    return $cors->handle($request, $type, $catch);
  }
}

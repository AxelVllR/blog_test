<?php
namespace App;

class App {

    /**
     * @var array
     */
    private $routes;

    public function __construct() {
        $this->routes = json_decode(file_get_contents('../routing/routes.json'), true);
    }

    public function run($request) {
        $exp = explode("/", $request);
        if(empty($exp[0])) {
            unset($exp[0]);
        }

        if(count($exp) > 1) {
           $this->getMultiRoute($exp, $request);
        } else {
            $this->getRoute($request);
        }
    }

    public function getMultiRoute($exp, $request) {
        $main = "/" . $exp[1];
        if(array_key_exists($request, $this->routes)) {
            $this->getRoute($request);
        } elseif(array_key_exists($main, $this->routes)) {
            if(isset($this->routes[$main]['sub_routes'])) {
                $need = $this->routes[$main]['class'];
                $sub = $this->routes[$main]['sub_routes'];
                if(isset($sub['regex'])) {
                    $regex = '/' . $sub['regex'] . '/';
                    if(!preg_match($regex, $exp[2])) {
                        $this->getRoute($main);
                    } else {
                        if(class_exists($need)) {
                            $class = new $need;
                            $methodToCall = $sub['method'];
                            if(method_exists($class, $methodToCall)) {
                                $class->$methodToCall($exp[2]);
                            } else {
                                $this->getRoute($main);
                            }
                        }
                    }
                } else {
                    $this->getRoute($main);
                }
            } else {
                $this->getRoute($main);
            }
        }
    }

    public function getRoute($request) {
        $renderer = new Renderer\Renderer();
        if(array_key_exists($request, $this->routes)) {
            $need = $this->routes[$request]['class'];
            if(class_exists($need)) {
                $class = new $need;
                $methodToCall = $this->routes[$request]['method'];
                if(method_exists($class, $methodToCall)) {
                    $class->$methodToCall();
                } else {
                    echo $renderer->display('404.html.twig');
                }

            } else {
                echo $renderer->display('404.html.twig');
            }
        } else {
            echo $renderer->display('404.html.twig');
        }
    }
}